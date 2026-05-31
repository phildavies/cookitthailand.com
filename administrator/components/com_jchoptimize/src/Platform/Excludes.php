<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/joomla-platform
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2020 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Platform;

use JchOptimize\Core\Interfaces\Excludes as ExcludesInterface;

defined('_JEXEC') or die('Restricted access');

class Excludes implements ExcludesInterface
{
    /**
     *
     * @param string $type
     * @param string $section
     *
     * @return array
     */
    public static function body(string $type, string $section = 'file'): array
    {
        if ($type == 'js') {
            if ($section == 'script') {
                return [
                    ['script' => 'var mapconfig90'],
                    ['script' => 'var addy']
                ];
            } else {
                return [
                    ['url' => 'assets.pinterest.com/js/pinit.js']
                ];
            }
        }

        if ($type == 'css') {
            return [];
        }

        return [];
    }

    /**
     *
     * @return string
     */
    public static function extensions(): string
    {
        //language=RegExp
        return '(?>components|modules|plugins/[^/]+|media(?!/system|/jui|/cms|/media|/css|/js|/images|/vendor|/templates)(?:/vendor)?)/';
    }

    /**
     *
     * @param string $type
     * @param string $section
     *
     * @return array
     */
    public static function head(string $type, string $section = 'file'): array
    {
        if ($type == 'js') {
            if ($section == 'script') {
                return [];
            } else {
                return [
                    ['url' => 'plugin_googlemap3'],
                    ['url' => '/jw_allvideos/'],
                    ['url' => '/tinymce/']
                ];
            }
        }

        if ($type == 'css') {
            return [];
        }

        return [];
    }

    /**
     *
     * @param string $url
     *
     * @return bool
     */
    public static function editors(string $url): bool
    {
        return (bool)preg_match('#/editors/#i', $url);
    }

    public static function smartCombine(): array
    {
        return [
            'media/(?:jui|system|cms)/',
            '/templates/',
            '.'
        ];
    }
}
