<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2024 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Route66\Route66\PluginTraits;

use Firecoders\Component\Route66\Administrator\Plugin\AnalyzerTrait as AnalyzerPluginTrait;

\defined('_JEXEC') or die;

trait AnalyzerTrait
{
    use AnalyzerPluginTrait;

    protected const RESOURCES = ['Page'];
}
