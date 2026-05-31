<?php

namespace JchOptimize\Core\Mvc;

use _JchOptimizeVendor\Joomla\Controller\AbstractController;
use Joomla\DI\ContainerAwareInterface;
use JchOptimize\Core\Container\ContainerAwareTrait;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;

abstract class Controller extends AbstractController implements ContainerAwareInterface, LoggerAwareInterface
{
    use ContainerAwareTrait;
    use LoggerAwareTrait;

    abstract public function execute();
}
