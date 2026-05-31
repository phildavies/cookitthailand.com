<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/joomla-platform
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2022 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Platform;

use JchOptimize\Core\Interfaces\Hooks as HooksInterface;
use JchOptimize\GetApplicationTrait;
use JchOptimize\Joomla\Plugin\PluginHelper;

use function defined;

defined('_JEXEC') or die('Restricted Access');

class Hooks implements HooksInterface
{
    use GetApplicationTrait;

    /**
     * @inheritDoc
     */
    public static function onPageCacheSetCaching(): bool
    {
        /** @var array<array-key, mixed> $results */
        $results = self::getApplication()->triggerEvent('onPageCacheSetCaching');

        return !in_array(false, $results, true);
    }

    /**
     * @inheritDoc
     */
    public static function onPageCacheGetKey(array $parts): array
    {
        $results = self::getApplication()->triggerEvent('onPageCacheGetKey');

        if (!empty($results)) {
            $parts = array_merge($parts, $results);
        }

        return $parts;
    }

    public static function onUserPostForm(): void
    {
        // Import the user plugin group.
        PluginHelper::importPlugin('user');
        self::getApplication()->triggerEvent('onUserPostForm');
    }

    public static function onUserPostFormDeleteCookie(): void
    {
        // Import the user plugin group.
        PluginHelper::importPlugin('user');
        self::getApplication()->triggerEvent('onUserPostFormDeleteCookie');
    }

    public static function onHttp2GetPreloads(array $preloads): array
    {
        return $preloads;
    }
}
