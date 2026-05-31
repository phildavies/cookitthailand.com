<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/core
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2022 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Core\Service;

use JchOptimize\Core\Cdn;
use JchOptimize\Core\FeatureHelpers\CdnDomains;
use JchOptimize\Core\FeatureHelpers\DynamicJs;
use JchOptimize\Core\FeatureHelpers\DynamicSelectors;
use JchOptimize\Core\FeatureHelpers\Fonts;
use JchOptimize\Core\FeatureHelpers\Http2Excludes;
use JchOptimize\Core\FeatureHelpers\LazyLoadExtended;
use JchOptimize\Core\FeatureHelpers\LCPImages;
use JchOptimize\Core\FeatureHelpers\ReduceDom;
use JchOptimize\Core\FeatureHelpers\ResponsiveImages;
use JchOptimize\Core\FeatureHelpers\Webp;
use JchOptimize\Core\Html\CacheManager;
use JchOptimize\Core\Html\FilesManager;
use JchOptimize\Core\Html\HtmlManager;
use JchOptimize\Core\Http2Preload;
use JchOptimize\Core\Registry;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use _JchOptimizeVendor\Laminas\EventManager\LazyListener;
use _JchOptimizeVendor\Laminas\EventManager\SharedEventManager;

use function defined;

defined('_JCH_EXEC') or die('Restricted access');
class FeatureHelpersProvider implements ServiceProviderInterface
{
    public function register(Container $container): void
    {
        $container->share(CdnDomains::class, function (Container $container): CdnDomains {
            return new CdnDomains($container, $container->get(Registry::class), $container->get(Cdn::class));
        }, \true);
        $container->share(DynamicJs::class, function (Container $container): DynamicJs {
            return new DynamicJs($container, $container->get(Registry::class), $container->get(CacheManager::class), $container->get(HtmlManager::class), $container->get(FilesManager::class));
        }, \true);
        $container->share(DynamicSelectors::class, function (Container $container): DynamicSelectors {
            return new DynamicSelectors($container, $container->get(Registry::class));
        }, \true);
        $container->share(Fonts::class, function (Container $container): Fonts {
            return new Fonts($container, $container->get(Registry::class), $container->get(HtmlManager::class));
        }, \true);
        $container->share(Http2Excludes::class, function (Container $container): Http2Excludes {
            return new Http2Excludes($container, $container->get(Registry::class), $container->get(Http2Preload::class));
        }, \true);
        $container->share(LazyLoadExtended::class, function (Container $container): LazyLoadExtended {
            return new LazyLoadExtended($container, $container->get(Registry::class), $container->get(HtmlManager::class));
        }, \true);
        $container->share(ReduceDom::class, function (Container $container): ReduceDom {
            return new ReduceDom($container, $container->get(Registry::class));
        }, \true);
        $container->share(Webp::class, function (Container $container): Webp {
            return new Webp($container, $container->get(Registry::class));
        }, \true);
        $container->share(ResponsiveImages::class, function (Container $container): ResponsiveImages {
            return new ResponsiveImages($container, $container->get(Registry::class));
        }, \true);
        $container->share(LCPImages::class, function (Container $container): LCPImages {
            return new LCPImages($container, $container->get(Registry::class), $container->get(Http2Preload::class));
        }, \true);
        //Set up events management
        /** @var SharedEventManager $sharedEvents */
        $sharedEvents = $container->get(SharedEventManager::class);
        $sharedEvents->attach(HtmlManager::class, 'preProcessHtml', new LazyListener([
            /** @see Fonts::checkPreconnects() */
            'listener' => Fonts::class,
            'method' => 'checkPreconnects',
        ], $container));
        $sharedEvents->attach(HtmlManager::class, 'postProcessHtml', new LazyListener([
            /** @see Fonts::appendOptimizedFontsToHead() */
            'listener' => Fonts::class,
            'method' => 'appendOptimizedFontsToHead',
        ], $container));
        $sharedEvents->attach(HtmlManager::class, 'postProcessHtml', new LazyListener([
            /** @see Fonts::addPreConnectsFontsToHead() */
            'listener' => Fonts::class,
            'method' => 'addPreConnectsFontsToHead',
        ], $container), 300);
        /*   $sharedEvents->attach(
             HtmlManager::class,
             'preProcessHtml',
             new LazyListener([
                 /** @see LazyLoadExtended::addCssLazyLoadAssetsToHtml() */
        /*           'listener' => LazyLoadExtended::class,
                        'method' => 'addCssLazyLoadAssetsToHtml'
                    ], $container)
                ); */
        $sharedEvents->attach(HtmlManager::class, 'postProcessHtml', new LazyListener([
            /** @see LazyLoadExtended::lazyLoadCssBackgroundImages() */
            'listener' => LazyLoadExtended::class,
            'method' => 'lazyLoadCssBackgroundImages',
        ], $container));
    }
}
