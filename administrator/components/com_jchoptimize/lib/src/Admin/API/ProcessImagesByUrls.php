<?php

namespace JchOptimize\Core\Admin\API;

use Exception;
use _JchOptimizeVendor\GuzzleHttp\Psr7\UriResolver;
use JchOptimize\ContainerFactory;
use JchOptimize\Core\Admin\AbstractHtml;
use JchOptimize\Core\Admin\Ajax\OptimizeImage;
use JchOptimize\Core\Admin\Helper as AdminHelper;
use JchOptimize\Core\Combiner;
use JchOptimize\Core\Html\FilesManager;
use JchOptimize\Core\Html\Processor as HtmlProcessor;
use JchOptimize\Core\Registry;
use JchOptimize\Core\SystemUri;
use JchOptimize\Core\Uri\UriConverter;
use JchOptimize\Core\Uri\UriNormalizer;
use JchOptimize\Core\Uri\Utils;
use Joomla\DI\Container;
use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

use function array_filter;
use function array_map;
use function array_merge;
use function array_shift;
use function array_unique;
use function array_values;
use function count;
use function file_exists;
use function filesize;
use function in_array;
use function preg_match;

class ProcessImagesByUrls extends \JchOptimize\Core\Admin\API\AbstractProcessImages
{
    private array $htmlArray;
    private array $images = [];
    private ?UriInterface $uri = null;
    private array $processedImages = [];
    public function __construct(Container $container, \JchOptimize\Core\Admin\API\MessageEventInterface $messageEventObj)
    {
        parent::__construct($container, $messageEventObj);
        $this->crawlHtmls();
    }
    public function getFilePackages(): array
    {
        [$files, $totalFileSize] = $this->initializeFileArray();
        do {
            if (empty($this->images) && count($this->htmlArray) > 0) {
                $this->images = $this->getImagesFromPendingHtml();
            }
            do {
                if (empty($this->images)) {
                    break;
                }
                $image = array_shift($this->images);
                $fileSize = filesize($image);
                if ($fileSize > $this->maxUploadFilesize) {
                    $this->messageEventObj->send('Skipping ' . AdminHelper::maskFileName($image) . ': Too large!');
                    continue;
                }
                $totalFileSize += $fileSize;
                if ($totalFileSize > $this->maxUploadFilesize) {
                    $this->prevFiles[] = $image;
                    $this->prevFileSize = $fileSize;
                    break;
                }
                $files['images'][] = $image;
                $this->processedImages[] = $image;
            } while (count($files['images']) < $this->getMaxFileUploads());
            if (count($files['images']) > 0) {
                $files['url'] = (string) $this->uri;
                return $files;
            }
        } while (count($this->images) > 0 || count($this->htmlArray) > 0);
        return $files;
    }
    private function crawlHtmls(): void
    {
        $oHtml = $this->getContainer()->get(AbstractHtml::class);
        $oHtml->setEventLogging(\true, $this->messageEventObj);
        $options = ['base_url' => (string) $this->params->get('pro_api_base_url', SystemUri::currentBaseFull()), 'crawl_limit' => (int) $this->params->get('pro_api_crawl_limit', 15)];
        try {
            $this->htmlArray = $oHtml->getCrawledHtmls($options);
        } catch (Exception $e) {
            $this->htmlArray = [];
        }
    }
    private function getImagesFromPendingHtml(): array
    {
        $container = ContainerFactory::getNewContainerInstance();
        $params = $container->get(Registry::class);
        $params->set('combine_files_enable', '1');
        $params->set('pro_smart_combine', '0');
        $params->set('javascript', '0');
        $params->set('css', '1');
        $params->set('css_minify', '0');
        $params->set('excludeCss', []);
        $params->set('excludeCssComponents', []);
        $params->set('replaceImports', '1');
        $params->set('phpAndExternal', '1');
        $params->set('inlineScripts', '1');
        $params->set('lazyload_enable', '0');
        $params->set('cookielessdomain_enable', '0');
        $params->set('optimizeCssDelivery_enable', '0');
        $params->set('csg_enable', '0');
        $aHtml = $this->getPendingHtmlArray();
        /** @var HtmlProcessor $oHtmlProcessor */
        $oHtmlProcessor = $container->getNewInstance(HtmlProcessor::class);
        $oHtmlProcessor->setHtml($aHtml['html']);
        $aHtmlImages = $oHtmlProcessor->processImagesForApi();
        try {
            $oHtmlProcessor->processCombineJsCss();
            $oFilesManager = $container->get(FilesManager::class);
            $aCssLinks = $oFilesManager->aCss;
            $oCombiner = $container->get(Combiner::class);
            $aResult = $oCombiner->combineFiles($aCssLinks[0], 'css');
            $aCssImages = array_unique(array_filter($aResult['images'], function ($a) {
                return $a instanceof UriInterface;
            }));
        } catch (Exception $e) {
            $aCssImages = [];
        }
        $images = array_merge($aHtmlImages, $aCssImages);
        $images = array_unique(array_filter($images));
        //Get the absolute file path of images on filesystem
        $uri = Utils::uriFor($aHtml['url']);
        $images = array_map(function ($a) use ($uri) {
            $uri = UriResolver::resolve($uri, UriNormalizer::normalize(Utils::uriFor($a)));
            return UriConverter::uriToFilePath($uri);
        }, $images);
        $images = array_filter($images, function ($a) {
            return preg_match('#' . OptimizeImage::$fileExtRegex . '#i', $a) && !in_array($a, $this->processedImages) && @file_exists($a);
        });
        //If option set, remove images already optimized
        if ($this->params->get('ignore_optimized', '1')) {
            $images = AdminHelper::filterOptimizedFiles($images);
        }
        $images = array_values(array_unique($images));
        if (!empty($images)) {
            $this->uri = $uri;
        }
        return $images;
    }
    private function getPendingHtmlArray(): array
    {
        return array_shift($this->htmlArray);
    }
    public function hasPendingImages(): bool
    {
        return !empty($this->htmlArray) || !empty($this->images);
    }
}
