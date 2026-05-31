<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/core
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2022 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Core\Admin\Ajax;

use Exception;
use Generator;
use _JchOptimizeVendor\GuzzleHttp\Client;
use _JchOptimizeVendor\GuzzleHttp\Exception\ConnectException;
use _JchOptimizeVendor\GuzzleHttp\Exception\RequestException;
use _JchOptimizeVendor\GuzzleHttp\Pool;
use _JchOptimizeVendor\GuzzleHttp\Psr7\MultipartStream;
use _JchOptimizeVendor\GuzzleHttp\Psr7\Request;
use _JchOptimizeVendor\GuzzleHttp\Psr7\Response;
use _JchOptimizeVendor\GuzzleHttp\Psr7\Utils as GuzzlePsr7Utils;
use _JchOptimizeVendor\GuzzleHttp\RequestOptions;
use JchOptimize\Core\Admin\API\MessageEventFactory;
use JchOptimize\Core\Admin\API\MessageEventInterface;
use JchOptimize\Core\Admin\API\ProcessImagesByFolders;
use JchOptimize\Core\Admin\API\ProcessImagesByUrls;
use JchOptimize\Core\Admin\API\ProcessImagesQueueInterface;
use JchOptimize\Core\Admin\Helper as AdminHelper;
use JchOptimize\Core\FeatureHelpers\Webp;
use JchOptimize\Core\Registry;
use JchOptimize\Platform\Paths;
use _JchOptimizeVendor\Psr\Http\Client\ClientInterface;
use stdClass;

use function array_map;
use function array_merge;
use function class_exists;
use function count;
use function defined;
use function file_exists;
use function json_encode;
use function print_r;
use function rtrim;
use function set_time_limit;
use function ucfirst;

defined('_JCH_EXEC') or die('Restricted access');
class OptimizeImage extends \JchOptimize\Core\Admin\Ajax\Ajax
{
    private MessageEventInterface $messageEventObj;
    public static string $backup_folder_name = 'jch_optimize_backup_images';
    public static string $fileExtRegex = '\\.(?:gif|jpe?g|png|webp)$';
    protected function __construct()
    {
        parent::__construct();
        $this->messageEventObj = MessageEventFactory::create($this->input->getString('evtMsg'));
        set_time_limit(0);
    }
    public function run()
    {
        $this->messageEventObj->initialize();
        /** @var object{subdirs: array, params: stdClass, filepack: stdClass[]} $cookieObj */
        $cookieObj = $this->messageEventObj->receive($this->input);
        if (!$cookieObj) {
            //EventStream::send('Client didn\'t send any data with request. Aborting...', 'apiError');
            exit;
        }
        $root = Paths::rootPath();
        if (isset($cookieObj->subdirs)) {
            $subDirs = array_map(function ($item) use ($root) {
                return $root . rtrim($item);
            }, $cookieObj->subdirs);
        }
        if (!empty($subDirs)) {
            $this->input->set('subdirs', $subDirs);
        }
        $apiParams = $cookieObj->params;
        $options = ['lossy' => (bool) $apiParams->lossy, 'save_metadata' => (bool) $apiParams->save_metadata, 'resize' => [], 'resize_mode' => $apiParams->pro_api_resize_mode, 'webp' => $apiParams->pro_next_gen_images, 'auth' => ['dlid' => $apiParams->pro_downloadid, 'secret' => $apiParams->hidden_api_secret], 'responsive' => \true];
        if (isset($cookieObj->filepack)) {
            $files = [];
            foreach ($cookieObj->filepack as $file) {
                $files[] = $file->path;
                if (isset($file->width) || isset($file->height)) {
                    $shortFileName = AdminHelper::contractFileName($file->path);
                    $options['resize'][$shortFileName]['width'] = (int) ($file->width ?? 0);
                    $options['resize'][$shortFileName]['height'] = (int) ($file->height ?? 0);
                }
            }
            $this->input->set('files', $files);
        }
        /** @var Client&ClientInterface $client */
        $client = $this->getContainer()->get(ClientInterface::class);
        $pool = new Pool($client, $this->getFilePackageRequests($options), ['concurrency' => (int) $this->getContainer()->get(Registry::class)->get('pro_api_concurrency', 5), 'options' => [RequestOptions::SYNCHRONOUS => \false, RequestOptions::TIMEOUT => 30, RequestOptions::VERSION => 2.0], 'fulfilled' => [$this, 'fulfilledRequests'], 'rejected' => [$this, 'rejectedRequests']]);
        $promise = $pool->promise();
        $promise->wait();
        $this->messageEventObj->send(Paths::getLogsPath(), 'complete');
        $this->messageEventObj->disconnect();
    }
    public function fulfilledRequests(Response $response, $index): void
    {
        $body = $response->getBody();
        $body->rewind();
        $contents = $body->getContents();
        /** @var object{
         *     success: bool,
         *     message: string,
         *     data: array< string, array{
         *                              0: stdClass,
         *                              1?: stdClass,
         *                              2?: array<string, stdClass[]>
         *                          }>
         *  } $dataArray
         */
        $dataArray = \json_decode($contents);
        //Check if response is formatted properly
        if (!isset($dataArray->success)) {
            $this->messageEventObj->send('Unknown response from server! Aborting...', 'apiError');
            $this->logger->error('Unknown response from server: ' . print_r($dataArray, \true));
            return;
        }
        //Handle Responses that are exceptions (ie, codes 403, 500)
        if (!$dataArray->success) {
            $this->messageEventObj->send($dataArray->message . '. Aborting...', 'apiError');
            return;
        }
        foreach ($dataArray->data as $i => $dataSet) {
            $originalFile = $index[$i];
            $maskedFileName = AdminHelper::maskFileName($originalFile);
            $message = $maskedFileName . ': ';
            if ($dataSet[0]->success) {
                $backupFile = self::getBackupFilename($originalFile);
                $fileMessage = ' optimized file.';
                if (!@file_exists($backupFile)) {
                    $overwriteOriginal = AdminHelper::copyImage($originalFile, $backupFile);
                    $fileMessage = ' backup file - ' . $backupFile;
                } else {
                    $overwriteOriginal = \true;
                }
                //Copy optimized file over original only if backup was successful
                if ($overwriteOriginal && AdminHelper::copyImage($dataSet[0]->data->kraked_url, $originalFile)) {
                    $message .= 'Optimized! You saved ' . $dataSet[0]->data->saved_bytes . ' bytes';
                    $this->messageEventObj->send($message, 'fileOptimized');
                    AdminHelper::markOptimized($originalFile);
                } else {
                    $message .= 'Could not copy' . $fileMessage;
                    $this->messageEventObj->send($message, 'optimizationFailed');
                }
            } else {
                $message .= $dataSet[0]->message;
                if ($dataSet[0]->code == 304) {
                    AdminHelper::markOptimized($originalFile);
                    $this->messageEventObj->send($message, 'alreadyOptimized');
                } else {
                    $this->messageEventObj->send($message, 'optimizationFailed');
                }
            }
            $this->logger->info($message);
            //handle WEBP generated images
            if (isset($dataSet[1])) {
                $webpMessage = $maskedFileName . ': ';
                if ($dataSet[1]->success) {
                    $webpFile = Webp::getWebpPath($originalFile);
                    if (AdminHelper::copyImage($dataSet[1]->data->webp_url, $webpFile)) {
                        $webpMessage .= 'Converted to WEBP! You saved ' . $dataSet[1]->data->webp_savings . ' more bytes.';
                        //If this file wasn't backed up before, save a backup now to facilitate restoration
                        $backupFile = self::getBackupFilename($originalFile);
                        $this->messageEventObj->send($webpMessage, 'webpGenerated');
                        if (!@file_exists($backupFile)) {
                            AdminHelper::copyImage($originalFile, $backupFile);
                        }
                    } else {
                        $webpMessage .= $dataSet[1]->message;
                        $this->messageEventObj->send($webpMessage);
                    }
                    $this->logger->info($webpMessage);
                } else {
                    $webpMessage .= $dataSet[1]->message;
                    $this->messageEventObj->send($webpMessage);
                }
                $this->logger->info($webpMessage);
            }
            if (isset($dataSet[2])) {
                $responsiveMessage = $maskedFileName . ': ';
                $success = 0;
                foreach ($dataSet[2] as $breakpoint => $result) {
                    $rsImagePath = AdminHelper::contractFileName($originalFile);
                    if ($result[0]->success) {
                        $success |= (int) AdminHelper::copyImage($result[0]->data->kraked_url, Paths::responsiveImagePath() . '/' . $breakpoint . '/' . $rsImagePath);
                    }
                    if ($result[1]->success) {
                        $fileParts = \pathinfo($rsImagePath);
                        $fileName = $fileParts['filename'];
                        $success |= (int) AdminHelper::copyImage($result[1]->data->webp_url, Paths::responsiveImagePath() . '/' . $breakpoint . '/' . $fileName . '.webp');
                    }
                }
                if ($success) {
                    $this->messageEventObj->send($responsiveMessage . 'Responsive images generated');
                }
            }
        }
    }
    public function rejectedRequests(RequestException|ConnectException $exception, $index): void
    {
        foreach ($index as $file) {
            $fileName = AdminHelper::maskFileName($file);
            $message = $fileName . ': Request failed with message: ' . $exception->getMessage();
            $this->messageEventObj->send($message, 'requestRejected');
        }
    }
    /**
     *
     * @param string $file
     *
     * @return string
     */
    protected function getBackupFilename(string $file): string
    {
        $backup_parent_dir = Paths::backupImagesParentDir();
        return $backup_parent_dir . self::$backup_folder_name . '/' . AdminHelper::contractFileName($file);
    }
    public function getFilePackageRequests($options): Generator
    {
        $filesFound = 0;
        $mode = $this->input->get('mode');
        /** @see ProcessImagesByUrls */
        /** @see ProcessImagesByFolders */
        $imageProcessorClass = '\\JchOptimize\\Core\\Admin\\API\\ProcessImages' . ucfirst($mode);
        if (class_exists($imageProcessorClass)) {
            /** @var ProcessImagesQueueInterface $imageProcessor */
            $imageProcessor = new $imageProcessorClass($this->getContainer(), $this->messageEventObj);
            while ($imageProcessor->hasPendingImages()) {
                $files = $imageProcessor->getFilePackages();
                if (empty($files['images'])) {
                    continue;
                }
                $uploadFiles = [];
                $filesFound += count($files['images']);
                $this->messageEventObj->send(count($files['images']), 'addFileCount');
                foreach ($files['images'] as $i => $file) {
                    try {
                        $contents = GuzzlePsr7Utils::tryFopen($file, 'r');
                    } catch (Exception) {
                        $contents = '';
                    }
                    if (file_exists($file)) {
                        $uploadFiles[] = ['name' => 'files[' . $i . ']', 'contents' => $contents, 'filename' => AdminHelper::contractFileName($file)];
                    }
                }
                if (isset($files['url'])) {
                    $options['url'] = $files['url'];
                }
                $data = ['name' => 'data', 'contents' => json_encode($options)];
                $body = array_merge($uploadFiles, [$data]);
                (yield $files['images'] => new Request('POST', 'https://api2.jch-optimize.net/', [], new MultipartStream($body)));
            }
        } else {
            $this->logger->error('Image Processor Class not found');
        }
        if ($filesFound === 0) {
            $this->messageEventObj->send(0, 'addFileCount');
        }
    }
}
