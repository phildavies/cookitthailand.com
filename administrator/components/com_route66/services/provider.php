<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Extension\Route66Component;
use Firecoders\Component\Route66\Administrator\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

require_once JPATH_SITE.'/administrator/components/com_route66/vendor/autoload.php';

return new class () implements ServiceProviderInterface {
    public function register(Container $container)
    {
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\Firecoders\\Component\\Route66'));
        $container->registerServiceProvider(new MVCFactory('\\Firecoders\\Component\\Route66'));
        $container->registerServiceProvider(new RouterFactory('\\Firecoders\\Component\\Route66'));

        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new Route66Component($container->get(ComponentDispatcherFactoryInterface::class));

                $component->setRegistry($container->get(Registry::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                $component->setRouterFactory($container->get(RouterFactoryInterface::class));

                return $component;
            }
        );
    }
};
