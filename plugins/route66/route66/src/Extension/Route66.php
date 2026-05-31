<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Route66\Route66\Extension;

\defined('_JEXEC') or die;

use Firecoders\Plugin\Route66\Route66\PluginTraits\AnalyzerTrait;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;

final class Route66 extends CMSPlugin implements SubscriberInterface
{
    use AnalyzerTrait;

    public static function getSubscribedEvents(): array
    {
        return [
            'onRoute66AnalyzerOptions' => 'onAnalyzerOptions',
        ];
    }

}
