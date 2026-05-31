<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2024 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Route66\Content\PluginTraits;

use Firecoders\Component\Route66\Administrator\Plugin\RouterTrait as RouterPluginTrait;

\defined('_JEXEC') or die;

trait RouterTrait
{
    use RouterPluginTrait;

    protected const RULES = ['Article', 'Category'];
}
