<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Extension\Service\Provider;

use Joomla\CMS\Cache\CacheControllerFactoryInterface;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Mail\MailerFactoryInterface;
use Joomla\CMS\MVC\Factory\ApiMVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Router\SiteRouter;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\Database\DatabaseInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use Joomla\Event\DispatcherInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class MVCFactory implements ServiceProviderInterface
{
    private $namespace;

    public function __construct(string $namespace)
    {
        $this->namespace = $namespace;
    }

    public function register(Container $container)
    {
        $container->set(
            MVCFactoryInterface::class,
            function (Container $container) {
                if (\Joomla\CMS\Factory::getApplication()->isClient('api')) {
                    $factory = new ApiMVCFactory($this->namespace);
                } else {
                    $factory = new \Firecoders\Component\Route66\Administrator\MVC\Factory\MVCFactory($this->namespace);
                }

                $factory->setFormFactory($container->get(FormFactoryInterface::class));
                $factory->setDispatcher($container->get(DispatcherInterface::class));
                $factory->setDatabase($container->get(DatabaseInterface::class));
                $factory->setSiteRouter($container->get(SiteRouter::class));
                $factory->setCacheControllerFactory($container->get(CacheControllerFactoryInterface::class));
                $factory->setUserFactory($container->get(UserFactoryInterface::class));
                $factory->setMailerFactory($container->get(MailerFactoryInterface::class));

                return $factory;
            }
        );
    }
}
