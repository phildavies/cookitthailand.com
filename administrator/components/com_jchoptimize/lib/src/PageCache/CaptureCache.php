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

namespace JchOptimize\Core\PageCache;

use Exception;
use JchOptimize\Core\Admin\Tasks;
use JchOptimize\Core\Exception\InvalidArgumentException;
use JchOptimize\Core\Helper;
use JchOptimize\Core\Htaccess;
use JchOptimize\Core\Registry;
use JchOptimize\Core\SystemUri;
use JchOptimize\Core\Uri\Utils;
use JchOptimize\Platform\Cache;
use JchOptimize\Platform\Paths;
use JchOptimize\Platform\Utility;
use Joomla\Filesystem\Folder;
use Joomla\Filesystem\Path;
use Joomla\Input\Input;
use _JchOptimizeVendor\Laminas\Cache\Exception\ExceptionInterface;
use _JchOptimizeVendor\Laminas\Cache\Exception\RuntimeException;
use _JchOptimizeVendor\Laminas\Cache\Pattern\CaptureCache as LaminasCaptureCache;
use _JchOptimizeVendor\Laminas\Cache\Storage\IterableInterface;
use _JchOptimizeVendor\Laminas\Cache\Storage\StorageInterface;
use _JchOptimizeVendor\Laminas\Cache\Storage\TaggableInterface;
use _JchOptimizeVendor\Psr\Http\Message\UriInterface;
use Throwable;

use function defined;
use function file_exists;
use function gzencode;
use function preg_replace;
use function strpos;
use function strtr;

defined('_JCH_EXEC') or die('Restricted access');
class CaptureCache extends \JchOptimize\Core\PageCache\PageCache
{
    /**
     * @var bool
     */
    protected bool $captureCacheEnabled = \true;
    /**
     * @var LaminasCaptureCache
     */
    private LaminasCaptureCache $captureCache;
    /**
     * @var string
     */
    private string $captureCacheId = '';
    /**
     * @var string
     */
    private string $startHtaccessLine = '## BEGIN CAPTURE CACHE - JCH OPTIMIZE ##';
    /**
     * @var string
     */
    private string $endHtaccessLine = '## END CAPTURE CACHE - JCH OPTIMIZE ##';
    /**
     * @param Registry $params
     * @param Input $input
     * @param StorageInterface $pageCacheStorage
     * @param StorageInterface&TaggableInterface&IterableInterface $taggableCache
     * @param LaminasCaptureCache $captureCache
     */
    public function __construct(Registry $params, Input $input, StorageInterface $pageCacheStorage, $taggableCache, LaminasCaptureCache $captureCache)
    {
        parent::__construct($params, $input, $pageCacheStorage, $taggableCache);
        $this->captureCache = $captureCache;
        if ($this->params->get('pro_cache_platform', '0')) {
            $this->captureCacheEnabled = \false;
        }
        $uri = $this->getCurrentPage();
        //Don't use capture cache when there's query
        if (!Utility::isAdmin() && $uri->getQuery() !== '') {
            $this->captureCacheEnabled = \false;
        }
        //Don't use capture cache when URL ends in index.php to avoid conflicts with CMS redirects
        if (!Utility::isAdmin() && \trim($uri->getPath(), '/') == \trim(SystemUri::basePath() . 'index.php', '/') && empty($uri->getQuery())) {
            $this->captureCacheEnabled = \false;
        }
    }
    public function getItems(): array
    {
        $items = parent::getItems();
        $filteredItems = [];
        //set http-request tag if a cache file exists for this item
        foreach ($items as $item) {
            $uri = Utils::uriFor($item['url']);
            $captureCacheId = $this->getCaptureCacheIdFromPage($uri);
            $item['http-request'] = $this->captureCache->has($captureCacheId) ? 'yes' : 'no';
            //filter by HTTP Requests
            if (!empty($this->filters['filter_http-request'])) {
                if ($item['http-request'] != $this->filters['filter_http-request']) {
                    continue;
                }
            }
            $filteredItems[] = $item;
        }
        //If we're sorting by http-request we'll need to re-sort
        if (strpos($this->lists['list_fullordering'], 'http-request') === 0) {
            $this->sortItems($filteredItems, $this->lists['list_fullordering']);
        }
        return $filteredItems;
    }
    private function getCaptureCacheIdFromPage(?UriInterface $page = null): string
    {
        $uri = (string) $page === '' || \is_null($page) ? $this->getCurrentPage() : $page;
        $id = $uri->getScheme() . '/' . $uri->getHost() . ($uri->getPort() ? ':' . $uri->getPort() : '') . '/' . $uri->getPath() . '/' . $uri->getQuery();
        $id .= '/index.html';
        return Path::clean($id);
    }
    /**
     * @return void
     * @throws ExceptionInterface
     */
    public function initialize(): void
    {
        $this->captureCacheId = $this->getCaptureCacheIdFromPage();
        //If user is logged in we'll need to set a cookie, so they won't see pages cached by another user
        if (!Utility::isGuest() && !$this->input->cookie->get('jch_optimize_no_cache_user_state') == 'user_logged_in') {
            $options = ['httponly' => \true, 'samesite' => 'Lax'];
            $this->input->cookie->set('jch_optimize_no_cache_user_state', 'user_logged_in', $options);
        } elseif (Utility::isGuest() && $this->input->cookie->get('jch_optimize_no_cache_user_state') == 'user_logged_in') {
            $options = ['expires' => 1, 'httponly' => \true, 'samesite' => 'Lax'];
            $this->input->cookie->set('jch_optimize_no_cache_user_state', '', $options);
        }
        parent::initialize();
    }
    public function store(string $html): string
    {
        //Tag should be set in parent::store()
        $html = parent::store($html);
        //This function will check for a valid tag before saving capture cache
        $this->setCaptureCache($html);
        return $html;
    }
    protected function setCaptureCache(string $html)
    {
        if ($this->getCachingEnabled() && $this->isCaptureCacheEnabled() && !empty($this->taggableCache->getTags($this->cacheId)) && !$this->captureCache->has($this->captureCacheId)) {
            try {
                $html = $this->tagCaptureCacheHtml($html);
                $this->captureCache->set($html, $this->captureCacheId);
                //Gzip
                $html = preg_replace('#and served using HTTP Request#', '\\0 (Gzipped)', $html);
                $htmlGz = gzencode($html, 9);
                $this->captureCache->set($htmlGz, $this->getGzippedCaptureCacheId($this->captureCacheId));
            } catch (Exception $e) {
            }
        }
    }
    private function tagCaptureCacheHtml(string $content): ?string
    {
        return preg_replace('#Cached by JCH Optimize on .*? GMT#', '\\0 and served using HTTP Request', $content);
    }
    private function getGzippedCaptureCacheId(string $id): string
    {
        return $id . '.gz';
    }
    public function deleteItemById(string $id): bool
    {
        $result = 1;
        try {
            $captureCacheId = $this->getCaptureCacheIdFromPageCacheId($id);
            $gzCaptureCacheId = $this->getGzippedCaptureCacheId($captureCacheId);
            $this->captureCache->remove($captureCacheId);
            $this->captureCache->remove($gzCaptureCacheId);
            $result &= (int) (!$this->captureCache->has($captureCacheId));
            $result &= (int) (!$this->captureCache->has($gzCaptureCacheId));
        } catch (RuntimeException) {
            //Failed to delete cache
            $result = \false;
        } catch (Throwable) {
            //Cache didn't exist
        }
        if ($result) {
            //Delete parent cache only if successful because tag will be deleted here
            $result &= (int) parent::deleteItemById($id);
        }
        return (bool) $result;
    }
    public function getCaptureCacheIdFromPageCacheId(string $id): string
    {
        $tags = $this->taggableCache->getTags($id);
        if (!empty($tags[1])) {
            return $this->getCaptureCacheIdFromPage(Utils::uriFor($tags[1]));
        }
        throw new InvalidArgumentException('No tags found for cache id');
    }
    public function deleteItemsByIds(array $ids): bool
    {
        $result = 1;
        foreach ($ids as $id) {
            $result &= (int) $this->deleteItemById($id);
        }
        return (bool) $result;
    }
    public function deleteAllItems(): bool
    {
        $result = 1;
        $result &= (int) $this->deleteCaptureCacheDir();
        //Only delete parent if successful, tags will be deleted here
        if ($result) {
            $result &= (int) parent::deleteAllItems();
        }
        return (bool) $result;
    }
    private function deleteCaptureCacheDir(): bool
    {
        try {
            if (file_exists(Paths::captureCacheDir())) {
                return Folder::delete(Paths::captureCacheDir());
            }
        } catch (Exception $e) {
            //Let's try another way
            try {
                if (!Helper::deleteFolder(Paths::captureCacheDir())) {
                    $this->logger->error('Error trying to delete Capture Cache dir: ' . $e->getMessage());
                }
            } catch (Exception $e) {
            }
        }
        return !file_exists(Paths::captureCacheDir());
    }
    /**
     * @return void
     */
    public function updateHtaccess(): void
    {
        $pluginState = Cache::isPageCacheEnabled($this->params, \true);
        //If Capture Cache not enabled just clean htaccess and leave
        if (!$pluginState || !$this->params->get('pro_capture_cache_enable', '1') || $this->params->get('pro_cache_platform', '0') || !$this->captureCacheEnabled) {
            $this->cleanHtaccess();
            return;
        }
        $captureCacheDir = strtr(Paths::captureCacheDir(), '\\', '/');
        $relCaptureCacheDir = strtr(Paths::captureCacheDir(\true), '\\', '/');
        $jchVersion = JCH_VERSION;
        $htaccessContents = <<<APACHECONFIG
<IfModule mod_headers.c>
\tHeader set X-Cached-By: "JCH Optimize v{$jchVersion}"
</IfModule>

<IfModule mod_rewrite.c>
\tRewriteEngine On
\t
\tRewriteRule "\\.html\\.gz\$" "-" [T=text/html,E=no-gzip:1,E=no-brotli:1,L]
\t
\t<IfModule mod_headers.c>
\t\t<FilesMatch "\\.html\\.gz\$" >
\t\t\tHeader set Content-Encoding gzip
\t\t\tHeader set Vary Accept-Encoding
\t\t</FilesMatch>
\t\t
\t\tRewriteRule .* - [E=JCH_GZIP_ENABLED:yes]
\t</IfModule>
\t
\t<IfModule !mod_headers.c>
\t\t<IfModule mod_mime.c>
\t\t \tAddEncoding gzip .gz
\t\t</IfModule>
\t\t
\t\tRewriteRule .* - [E=JCH_GZIP_ENABLED:yes]
\t</IfModule>
\t
\tRewriteCond %{ENV:JCH_GZIP_ENABLED} ^yes\$
\tRewriteCond %{HTTP:Accept-Encoding} gzip
\tRewriteRule .* - [E=JCH_GZIP:.gz]
\t
\tRewriteRule .* - [E=JCH_SCHEME:http]
\t
\tRewriteCond %{HTTPS} on [OR]
\tRewriteCond %{SERVER_PORT} ^443\$
\tRewriteRule .* - [E=JCH_SCHEME:https]
    
\tRewriteCond %{REQUEST_METHOD} ^GET 
\tRewriteCond %{HTTP_COOKIE} !jch_optimize_no_cache
\tRewriteCond "{$captureCacheDir}/%{ENV:JCH_SCHEME}/%{HTTP_HOST}%{REQUEST_URI}/%{QUERY_STRING}/index\\.html%{ENV:JCH_GZIP}" -f
\tRewriteRule .* "{$relCaptureCacheDir}/%{ENV:JCH_SCHEME}/%{HTTP_HOST}%{REQUEST_URI}/%{QUERY_STRING}/index.html%{ENV:JCH_GZIP}" [L]
</IfModule>
APACHECONFIG;
        try {
            Htaccess::updateHtaccess($htaccessContents, [$this->startHtaccessLine, $this->endHtaccessLine], Tasks::$endHtaccessLine);
        } catch (Exception $e) {
        }
    }
    public function cleanHtaccess(): void
    {
        Htaccess::cleanHtaccess([$this->startHtaccessLine, $this->endHtaccessLine]);
    }
    public function hasCaptureCache(UriInterface $uri): bool
    {
        $captureCacheId = $this->getCaptureCacheIdFromPage($uri);
        return $this->captureCache->has($captureCacheId);
    }
}
