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

namespace JchOptimize\Core;

use FilesystemIterator;
use JchOptimize\Core\Uri\Utils;
use JchOptimize\Platform\Paths;
use Joomla\Filesystem\Folder;
use _JchOptimizeVendor\Psr\Http\Message\UriInterface;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use SplFileInfo;

use function array_map;
use function defined;
use function file_exists;
use function is_object;
use function preg_match;
use function preg_replace;
use function rmdir;
use function unlink;

defined('_JCH_EXEC') or die('Restricted access');
/**
 * Some helper functions
 *
 */
class Helper
{
    /**
     * Checks if file (can be external) exists
     *
     * @param   string  $sPath
     *
     * @return bool
     */
    public static function fileExists(string $sPath): bool
    {
        if (\str_starts_with($sPath, 'http')) {
            $sFileHeaders = @\get_headers($sPath);
            return $sFileHeaders !== \false && !\str_contains($sFileHeaders[0], '404');
        } else {
            return file_exists($sPath);
        }
    }
    public static function isMsieLT10(): bool
    {
        $browser = \JchOptimize\Core\Browser::getInstance();
        return $browser->getBrowser() == 'Internet Explorer' && \version_compare($browser->getVersion(), '10', '<');
    }
    public static function cleanReplacement(string $string): string
    {
        return \strtr($string, ['\\' => '\\\\', '$' => '\\$']);
    }
    /**
     * @return string
     * @deprecated
     */
    public static function getBaseFolder(): string
    {
        return \JchOptimize\Core\SystemUri::basePath();
    }
    public static function strReplace(string $search, string $replace, string $subject): string
    {
        return (string) \str_replace(self::cleanPath($search), $replace, self::cleanPath($subject));
    }
    public static function cleanPath(string $str): string
    {
        return \str_replace(['\\\\', '\\'], '/', $str);
    }
    /**
     * Determine if document is of XHTML doctype
     *
     * @param   string  $html
     *
     * @return bool
     */
    public static function isXhtml(string $html): bool
    {
        return (bool) preg_match('#^\\s*+(?:<!DOCTYPE(?=[^>]+XHTML)|<\\?xml.*?\\?>)#i', \trim($html));
    }
    /**
     * Determines if document is of html5 doctype
     *
     * @param   string  $html
     *
     * @return bool        True if doctype is html5
     */
    public static function isHtml5(string $html): bool
    {
        return (bool) preg_match('#^<!DOCTYPE html>#i', \trim($html));
    }
    public static function getArray(mixed $value): array
    {
        if (\is_array($value)) {
            $array = $value;
        } elseif (\is_string($value)) {
            $array = \explode(',', \trim($value));
        } elseif (is_object($value)) {
            $array = (array) $value;
        } else {
            $array = [];
        }
        if (!empty($array)) {
            $array = array_map(function ($v) {
                if (\is_string($v)) {
                    return \trim($v);
                } elseif (is_object($v)) {
                    return (array) $v;
                } else {
                    return $v;
                }
            }, $array);
        }
        return \array_filter($array);
    }
    public static function validateHtml(string $html): bool
    {
        return (bool) preg_match('#^(?>(?><?[^<]*+)*?<html(?><?[^<]*+)*?<head(?><?[^<]*+)*?</head\\s*+>)(?><?[^<]*+)*?' . '<body.*</body\\s*+>(?><?[^<]*+)*?</html\\s*+>#is', $html);
    }
    /**
     *
     * @param   string[]  $needles   Array of excluded values to compare against
     * @param   string    $haystack  The string we're testing to see if it was excluded
     * @param   string    $type      (css|js) No longer used
     *
     * @return bool
     */
    public static function findExcludes(array $needles, string $haystack, string $type = ''): bool
    {
        if (empty($needles)) {
            return \false;
        }
        foreach ($needles as $needle) {
            //Remove all spaces from test string and excluded string
            $needle = preg_replace('#\\s#', '', $needle);
            $haystack = preg_replace('#\\s#', '', $haystack);
            if ($needle && \str_contains(\htmlspecialchars_decode($haystack), $needle)) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @param   string[]  $needles
     * @param   string    $haystack
     *
     * @return bool
     */
    public static function findMatches(array $needles, string $haystack): bool
    {
        return self::findExcludes($needles, $haystack);
    }
    public static function extractUrlsFromSrcset($srcSet): array
    {
        $strings = \explode(',', $srcSet);
        $aUrls = array_map(function ($v) {
            $aUrlString = \explode(' ', \trim($v));
            return Utils::uriFor(\array_shift($aUrlString));
        }, $strings);
        return $aUrls;
    }
    /**
     * Utility function to convert a rule set to a unique class
     *
     * @param   string  $selectorGroup
     *
     * @return string
     */
    public static function cssSelectorsToClass(string $selectorGroup): string
    {
        return '_jch-' . preg_replace('#[^0-9a-z_-]#i', '', $selectorGroup);
    }
    public static function deleteFolder(string $folder): bool
    {
        $it = new RecursiveDirectoryIterator($folder, FilesystemIterator::SKIP_DOTS);
        $files = new RecursiveIteratorIterator($it, RecursiveIteratorIterator::CHILD_FIRST);
        /** @var SplFileInfo $file */
        foreach ($files as $file) {
            if ($file->isDir()) {
                rmdir($file->getPathname());
            } else {
                unlink($file->getPathname());
            }
        }
        rmdir($folder);
        return !file_exists($folder);
    }
    /**
     * Checks if a Uri is valid
     *
     * @param   UriInterface  $uri
     *
     * @return bool
     */
    public static function uriInvalid(UriInterface $uri): bool
    {
        if ((string) $uri == '') {
            return \true;
        }
        if ($uri->getScheme() == '' && $uri->getAuthority() == '' && $uri->getQuery() == '' && $uri->getFragment() == '') {
            if ($uri->getPath() == '/' || $uri->getPath() == \JchOptimize\Core\SystemUri::basePath()) {
                return \true;
            }
        }
        return \false;
    }
    /**
     * @return false|int
     *
     * @psalm-return 0|1|false
     */
    public static function isStaticFile(string $filePath)
    {
        return preg_match('#\\.(?:css|js|png|jpe?g|gif|bmp|webp|svg)$#i', $filePath);
    }
    public static function createCacheFolder(): void
    {
        if (!file_exists(Paths::cacheDir())) {
            try {
                Folder::create(Paths::cacheDir());
            } catch (\Exception $exception) {
            }
        }
    }
}
