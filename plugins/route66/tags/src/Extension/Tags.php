<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Route66\Tags\Extension;

\defined('_JEXEC') or die;

use Firecoders\Plugin\Route66\Tags\PluginTraits\RouterTrait;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;

final class Tags extends CMSPlugin implements SubscriberInterface
{
    use RouterTrait;

    public static function getSubscribedEvents(): array
    {
        return [
            'onRoute66RouterForm'  => 'onRouterForm',
            'onRoute66RouterRules' => 'onRouterRules',
        ];
    }

}
