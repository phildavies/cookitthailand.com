<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Route66\Menus\Extension;

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Plugin\SitemapTrait;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;

final class Menus extends CMSPlugin implements SubscriberInterface
{
    use SitemapTrait;

    public static function getSubscribedEvents(): array
    {
        return [
            'onRoute66SitemapForm'       => 'onSitemapForm',
            'onRoute66SitemapItems'      => 'onSitemapItems',
            'onRoute66SitemapItemsCount' => 'onSitemapItemsCount',
        ];
    }

}
