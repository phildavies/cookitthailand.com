<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Service;

use Joomla\CMS\Application\CMSApplicationInterface;
use Joomla\CMS\MVC\Factory\MVCFactory as CoreMVCFactory;
use Joomla\Input\Input;
use Psr\Container\ContainerInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class MVCFactory extends CoreMVCFactory
{
    public function createModel($name, $prefix = '', $config = [], ?CMSApplicationInterface $app = null, ?ContainerInterface $container = null)
    {
        $name = $this->convertName($name);

        return parent::createModel($name, $prefix, $config, $app, $container);
    }

    public function createView($name, $prefix = '', $type = '', $config = [], ?CMSApplicationInterface $app = null, ?ContainerInterface $container = null)
    {
        $name = $this->convertName($name);

        return parent::createView($name, $prefix, $type, $config, $app, $container);
    }

    public function createController($name, $prefix = '', $config = [], ?CMSApplicationInterface $app = null, ?Input $input = null, ?ContainerInterface $container = null)
    {
        $name = $this->convertName($name);

        return parent::createController($name, $prefix, $config, $app, $input, $container);
    }

    public function createTable($name, $prefix = '', $config = [])
    {
        $name = $this->convertName($name);

        return parent::createTable($name, $prefix, $config);
    }

    private function convertName(string $name): string
    {
        if ($name === 'aitools') {
            $name = 'AITools';
        } elseif ($name === 'aitool') {
            $name = 'AITool';
        } elseif ($name === 'contentanalysis') {
            $name = 'ContentAnalysis';
        } elseif ($name === 'crawlertask') {
            $name = 'CrawlerTask';
        }

        return $name;
    }
}
