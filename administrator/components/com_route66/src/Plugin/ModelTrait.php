<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2024 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Plugin;

\defined('_JEXEC') or die;

trait ModelTrait
{
    protected $models = [];

    private function getModel(string $name)
    {
        if (!isset($this->models[$name])) {
            $className           = '\\Firecoders\\Plugin\\Route66\\'.ucfirst($this->_name).'\\Model\\'.$name;
            $this->models[$name] = new $className(['ignore_request' => true]);
        }

        return $this->models[$name];
    }
}
