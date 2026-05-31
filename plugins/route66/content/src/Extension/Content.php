<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Route66\Content\Extension;

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Plugin\SitemapTrait;
use Firecoders\Plugin\Route66\Content\PluginTraits\AnalyzerTrait;
use Firecoders\Plugin\Route66\Content\PluginTraits\RouterTrait;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;

final class Content extends CMSPlugin implements SubscriberInterface
{
    use AnalyzerTrait;
    use SitemapTrait;
    use RouterTrait;

    public static function getSubscribedEvents(): array
    {
        return [
            'onRoute66SitemapForm'         => 'onSitemapForm',
            'onRoute66SitemapItems'        => 'onSitemapItems',
            'onRoute66SitemapItemsCount'   => 'onSitemapItemsCount',
            'onRoute66RouterForm'          => 'onRouterForm',
            'onRoute66RouterRules'         => 'onRouterRules',
            'onRoute66AnalyzerDisplay'     => 'onAnalyzerDisplay',
            'onRoute66AnalyzerOptions'     => 'onAnalyzerOptions',
            'onRoute66AnalyzerSave'        => 'onAnalyzerSave',
            'onRoute66AnalyzerUrl'         => 'onAnalyzerUrl',
            'onRoute66AnalyzerResourceKey' => 'onAnalyzerResourceKey',
            'onRoute66AnalyzerResourceId'  => 'onAnalyzerResourceId',
        ];
    }

}
