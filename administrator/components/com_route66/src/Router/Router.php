<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2024 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Router;

use Joomla\CMS\Factory;
use Joomla\CMS\Menu\AbstractMenu;
use Joomla\CMS\Plugin\PluginHelper;

\defined('_JEXEC') or die;

final class Router
{
    public function __construct()
    {
        $menu  = AbstractMenu::getInstance('site');
        $rules = $this->loadRules();

        new Builder($rules, $menu);

        if (Factory::getApplication()->isClient('site')) {
            new Parser($rules, $menu);
        }
    }

    private function loadRules()
    {
        PluginHelper::importPlugin('route66');
        $rules = Factory::getApplication()->triggerEvent('onRoute66RouterRules');

        return $rules;
    }
}
