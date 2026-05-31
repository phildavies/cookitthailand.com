<?php

namespace JchOptimize\Core\Admin\API;

use JchOptimize\Core\Admin\Helper as AdminHelper;
use JchOptimize\Core\Registry;
use Joomla\DI\Container;
use Joomla\DI\ContainerAwareInterface;
use JchOptimize\Core\Container\ContainerAwareTrait;

use function connection_aborted;
use function ini_get;

abstract class AbstractProcessImages implements \JchOptimize\Core\Admin\API\ProcessImagesQueueInterface, ContainerAwareInterface
{
    use ContainerAwareTrait;

    protected float $maxUploadFilesize;
    protected float $maxFileUploads;
    protected Registry $params;
    protected array $prevFiles = [];
    protected float $prevFileSize = 0;
    protected \JchOptimize\Core\Admin\API\MessageEventInterface $messageEventObj;
    public function __construct(Container $container, \JchOptimize\Core\Admin\API\MessageEventInterface $messageEventObj)
    {
        $this->setContainer($container);
        $this->params = $this->container->get(Registry::class);
        $this->messageEventObj = $messageEventObj;
        $maxFileSize = $this->params->get('pro_api_max_size', '2M') ?: ini_get('upload_max_filesize');
        $this->maxUploadFilesize = 0.8 * AdminHelper::stringToBytes($maxFileSize);
        $this->maxFileUploads = 0.8 * (int) ini_get('max_file_uploads');
    }
    protected function initializeFileArray(): array
    {
        $files = ['images' => $this->prevFiles];
        $totalFileSize = $this->prevFileSize;
        $this->prevFiles = [];
        $this->prevFileSize = 0;
        return [$files, $totalFileSize];
    }
    protected function getMaxFileUploads()
    {
        if (connection_aborted()) {
            exit;
        }
        $numFiles = $this->params->get('pro_api_num_files');
        if ($numFiles) {
            return \min($numFiles, $this->maxFileUploads);
        }
        return $this->maxFileUploads;
    }
    abstract public function getFilePackages(): array;
}
