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

namespace JchOptimize\Service;

use JchOptimize\Core\Container\Container;
use JchOptimize\Core\Spatie\CrawlQueues\CacheCrawlQueue;
use JchOptimize\Model\ReCache;
use JchOptimize\Model\ReCacheCliJ3;
use JchOptimize\Core\Container\ServiceProviderInterface;
use JchOptimize\Core\Registry;
use Psr\Log\LoggerInterface;

class ReCacheProvider implements ServiceProviderInterface
{

    public function register(Container $container): void
    {
        $container->share(
            ReCache::class,
            function (Container $container): ReCache {
                return new ReCache(
                    $container->get(Registry::class),
                    $container->get(CacheCrawlQueue::class),
                    $container->get(LoggerInterface::class)
                );
            }
        );
    }
}
