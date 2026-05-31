<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2024 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Plugin;

use Joomla\CMS\Component\ComponentHelper;

\defined('_JEXEC') or die;

trait ExtensionTrait
{
    private function isInstalled(): bool
    {
        return (bool) ComponentHelper::isInstalled('com_'.strtolower($this->_name));
    }
}
