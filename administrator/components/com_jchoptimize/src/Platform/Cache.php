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

namespace JchOptimize\Platform;

use JchOptimize\ContainerFactory;
use JchOptimize\Core\Html\Processor;
use JchOptimize\Core\Interfaces\Cache as CacheInterface;
use JchOptimize\Core\Registry;
use JchOptimize\GetApplicationTrait;
use JchOptimize\Joomla\Plugin\PluginHelper;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Session\Session;

use function defined;
use function file_exists;
use function preg_replace;

use const JPATH_PLUGINS;

defined('_JEXEC') or die('Restricted Access');

class Cache implements CacheInterface
{
    use GetApplicationTrait;

    public static function cleanThirdPartyPageCache(): void
    {
        //Clean Joomla Cache
        $cache = Factory::getCache();
        $groups = ['page', 'pce'];

        foreach ($groups as $group) {
            // @phpstan-ignore-next-line
            $cache->clean($group);
        }

        $app = self::getApplication();

        if ($app instanceof CMSApplication) {
            //Clean LiteSpeed Cache
            if (file_exists(JPATH_PLUGINS . '/system/lscache/lscache.php')) {
                $app->triggerEvent('onLSCacheExpired');
            }

            if (!$app->isClient('cli')) {
                $app->setHeader('X-LiteSpeed-Purge', '*');
            }
        }
    }

    /**
     * @param array|null $data
     *
     * @return array{headers: array{array-key: array{name:string, value:string}}, body:string}|null
     */
    public static function prepareDataFromCache(?array $data): ?array
    {
        // The following code searches for a token in the cached page and replaces it with the proper token.
        /** @var array{headers: array{array-key: array{name:string, value:string}}, body:string}|null $data */
        if (isset($data['body'])) {
            $token = Session::getFormToken();
            $search = '#<input type="?hidden"? name="?[\da-f]{32}"? value="?1"?\s*/?>#';
            $replacement = '<input type="hidden" name="' . $token . '" value="1">';
            $data['body'] = preg_replace($search, $replacement, $data['body']);

            $container = ContainerFactory::getNewContainerInstance();
            /** @var Processor $htmlProcessor */
            $htmlProcessor = $container->getNewInstance(Processor::class);
            $htmlProcessor->setHtml($data['body']);
            $htmlProcessor->processDataFromCacheScriptToken($token);

            $data['body'] = $htmlProcessor->getHtml();
        }

        return $data;
    }

    /**
     * @param array $data
     * @return void
     */
    public static function outputData(array $data): void
    {
        $app = self::getApplication();

        if ($app instanceof CMSApplication) {
            /** @var array{headers:array<array-key, array{name:string, value:string}>, body:string} $data */
            if (!empty($data['headers'])) {
                foreach ($data['headers'] as $header) {
                    $app->setHeader($header['name'], $header['value']);
                }
            }

            $app->setBody($data['body']);

            echo $app->toString((bool)$app->get('gzip'));

            $app->close();
        }
    }

    /**
     * @param Registry $params
     * @param bool $nativeCache
     *
     * @return bool
     */
    public static function isPageCacheEnabled(Registry $params, bool $nativeCache = false): bool
    {
        $integratedPageCache = 'jchoptimizepagecache';

        if (!$nativeCache) {
            /** @var string $integratedPageCache */
            $integratedPageCache = $params->get('pro_page_cache_integration', 'jchoptimizepagecache');
        }

        return PluginHelper::isEnabled('system', $integratedPageCache);
    }

    public static function getCacheNamespace(bool $pageCache = false): string
    {
        if ($pageCache) {
            return 'jchoptimizepagecache';
        }

        return 'jchoptimizecache';
    }

    public static function isCaptureCacheIncompatible(): bool
    {
        return false;
    }

    public static function getPageCacheNamespace(): string
    {
        return 'jchoptimizepagecache';
    }

    public static function getGlobalCacheNamespace(): string
    {
        return 'jchoptimizecache';
    }

    public static function getTaggableCacheNamespace(): string
    {
        return 'jchoptimizetags';
    }
}
