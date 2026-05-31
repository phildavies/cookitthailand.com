<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/core
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2023 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Core\Container;

use JchOptimize\ContainerFactory;
use JchOptimize\Core\Service\CachingConfigurationProvider;
use JchOptimize\Core\Service\CachingProvider;
use JchOptimize\Core\Service\CallbackProvider;
use JchOptimize\Core\Service\CoreProvider;
use JchOptimize\Core\Service\FeatureHelpersProvider;
use JchOptimize\Core\Service\IlluminateViewFactoryProvider;
use JchOptimize\Core\Service\SpatieProvider;
use _JchOptimizeVendor\Laminas\EventManager\SharedEventManager;
use _JchOptimizeVendor\Laminas\EventManager\SharedEventManagerInterface;

use function is_null;

abstract class AbstractContainerFactory
{
    /**
     * @var Container|null
     */
    protected static ?\JchOptimize\Core\Container\Container $instance = null;
    /**
     * Used to create a new global instance of Joomla/DI/Container or in cases where the container isn't
     * accessible by dependency injection
     *
     * @return Container
     */
    public static function getContainer(): \JchOptimize\Core\Container\Container
    {
        if (self::$instance === null) {
            self::$instance = self::getNewContainerInstance();
        }
        return self::$instance;
    }
    /**
     * Used to return a new instance of the Container when we're making changes we don't want to affect the
     * global container
     *
     * @return Container
     */
    public static function getNewContainerInstance(): \JchOptimize\Core\Container\Container
    {
        $ContainerFactory = new ContainerFactory();
        $container = new \JchOptimize\Core\Container\Container();
        $ContainerFactory->registerCoreProviders($container);
        $ContainerFactory->registerPlatformProviders($container);
        return $container;
    }
    /**
     * @param Container $container
     *
     * @return void
     */
    protected function registerCoreProviders(\JchOptimize\Core\Container\Container $container): void
    {
        $container->alias(SharedEventManager::class, SharedEventManagerInterface::class)->share(SharedEventManagerInterface::class, new SharedEventManager(), \true)->registerServiceProvider(new CoreProvider())->registerServiceProvider(new CachingConfigurationProvider())->registerServiceProvider(new CallbackProvider())->registerServiceProvider(new CachingProvider());
        if (JCH_PRO) {
            $container->registerServiceProvider(new FeatureHelpersProvider())->registerServiceProvider(new SpatieProvider());
        }
    }
    /**
     * To be implemented by JchOptimize/Container to attach service providers specific to the particular platform
     *
     * @param Container $container
     *
     * @return void
     */
    abstract protected function registerPlatformProviders(\JchOptimize\Core\Container\Container $container): void;
    /**
     * For use with test cases
     */
    public static function destroy(): void
    {
        self::$instance = null;
    }
}
