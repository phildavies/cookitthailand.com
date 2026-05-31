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

use Exception;
use _JchOptimizeVendor\GuzzleHttp\Psr7\Uri;
use _JchOptimizeVendor\GuzzleHttp\Psr7\UriResolver;
use JchOptimize\Core\Exception\RuntimeException;
use JchOptimize\Core\FeatureHelpers\CdnDomains;
use JchOptimize\Core\FeatureHelpers\Fonts;
use JchOptimize\Core\Uri\UriConverter;
use JchOptimize\Core\Uri\Utils;
use Joomla\DI\ContainerAwareInterface;
use JchOptimize\Core\Container\ContainerAwareTrait;
use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

use function array_column;
use function array_merge;
use function array_reduce;
use function array_unique;
use function count;
use function current;
use function explode;
use function implode;
use function next;
use function preg_match;
use function reset;
use function trim;

\defined('_JCH_EXEC') or die('Restricted access');
class Cdn implements ContainerAwareInterface
{
    use ContainerAwareTrait;

    public string $scheme = '';
    /** @var array<string, array{domain:UriInterface, extensions:string}>|null */
    protected ?array $domains = null;
    /** @var array<string, UriInterface> */
    protected array $filePaths = [];
    /** @var string[]|null */
    protected ?array $cdnFileTypes = null;
    private \JchOptimize\Core\Registry $params;
    private bool $enabled;
    private string $startHtaccessLine = '## BEGIN CDN CORS POLICY - JCH OPTIMIZE ##';
    private string $endHtaccessLine = '## END CDN CORS POLICY - JCH OPTIMIZE ##';
    public function __construct(\JchOptimize\Core\Registry $params)
    {
        $this->params = $params;
        $this->enabled = (bool) $this->params->get('cookielessdomain_enable', '0');
        $this->scheme = match ((string) $params->get('cdn_scheme', '0')) {
            '1' => 'http',
            '2' => 'https',
            default => '',
        };
    }
    /**
     * Returns an array of file types that will be loaded by CDN
     *
     * @return string[]
     * @throws RuntimeException
     */
    public function getCdnFileTypes(): array
    {
        if ($this->cdnFileTypes === null) {
            $this->initialize();
        }
        if ($this->cdnFileTypes !== null) {
            return $this->cdnFileTypes;
        }
        throw new RuntimeException('CDN file types not initialized');
    }
    private function initialize(): void
    {
        /** @var string[] $staticFiles1Array */
        $staticFiles1Array = $this->params->get('staticfiles', self::getStaticFiles());
        /** @var array<string, array{domain:UriInterface, extensions:string}> $domainArray */
        $domainArray = [];
        $this->cdnFileTypes = [];
        if ($this->enabled) {
            /** @var string $domain1 */
            $domain1 = $this->params->get('cookielessdomain', '');
            if (trim($domain1) != '') {
                /** @var string[] $customExtns */
                $customExtns = $this->params->get('pro_customcdnextensions', []);
                $sStaticFiles1 = implode('|', array_merge($staticFiles1Array, $customExtns));
                $domainArray['domain1']['domain'] = $this->prepareDomain($domain1);
                $domainArray['domain1']['extensions'] = $sStaticFiles1;
            }
            if (JCH_PRO) {
                /** @see CdnDomains::addCdnDomains() */
                $this->container->get(CdnDomains::class)->addCdnDomains($domainArray);
            }
            if (JCH_PRO && $this->params->get('pro_preconnect_domains_enable', '0')) {
                /** @see Fonts::pushDomainsToPreconnectArray() */
                $this->container->get(Fonts::class)->pushDomainsToPreconnectArray(array_column($domainArray, 'domain'));
            }
        }
        $this->domains = $domainArray;
        if (!empty($this->domains)) {
            foreach ($this->domains as $domains) {
                $this->cdnFileTypes = array_merge($this->cdnFileTypes, explode('|', $domains['extensions']));
            }
            $this->cdnFileTypes = array_unique($this->cdnFileTypes);
        }
    }
    /**
     * Returns array of default static files to load from CDN
     *
     *
     * @return string[] Array of file type extensions
     */
    public static function getStaticFiles(): array
    {
        return ['css', 'js', 'jpe?g', 'gif', 'png', 'ico', 'bmp', 'pdf', 'webp', 'svg'];
    }
    /**
     *
     * @param string $domain
     *
     * @return UriInterface
     */
    public function prepareDomain(string $domain): UriInterface
    {
        //If scheme not included then we need to add forward slashes to make UriInterfaces
        //implementations recognize the domain
        if (!preg_match('#^(?:[^:/]++:|//)#', trim($domain))) {
            $domain = '//' . $domain;
        }
        return Utils::uriFor($domain)->withScheme($this->scheme);
    }
    /**
     * @param UriInterface $uri
     * @param UriInterface|null $origPath
     *
     * @return UriInterface
     */
    public function loadCdnResource(UriInterface $uri, ?UriInterface $origPath = null): UriInterface
    {
        $domains = $this->getCdnDomains();
        if (empty($origPath)) {
            $origPath = $uri;
        }
        //if disabled or no domain is configured abort
        if (!$this->enabled || empty($domains) || $this->domains === null) {
            return $origPath;
        }
        //If file already loaded on CDN return
        if ($this->isFileOnCdn($uri)) {
            return $origPath;
        }
        //We're now ready to load path on CDN but let's remove query first
        $path = $uri->getPath();
        //If we haven't matched a cdn domain to this file yet then find one.
        if (!isset($this->filePaths[$path])) {
            $this->filePaths[$path] = $this->selectDomain($this->domains, $uri);
        }
        if ((string) $this->filePaths[$path] === '') {
            return $origPath;
        }
        return $this->filePaths[$path];
    }
    /**
     * @return array<string, array{domain:UriInterface, extensions:string}>
     */
    public function getCdnDomains(): array
    {
        if ($this->domains === null) {
            $this->initialize();
        }
        if ($this->domains !== null) {
            return $this->domains;
        }
        throw new RuntimeException('CDN Domains not initialized');
    }
    public function isFileOnCdn(UriInterface $uri): bool
    {
        foreach ($this->getCdnDomains() as $domainArray) {
            if ($uri->getHost() === $domainArray['domain']->getHost()) {
                return \true;
            }
        }
        return \false;
    }
    public function updateHtaccess(): void
    {
        $htaccessDelimiters = ['## BEGIN CDN CORS POLICY - JCH OPTIMIZE ##', '## END CDN CORS POLICY - JCH OPTIMIZE ##'];
        $origin = \JchOptimize\Core\SystemUri::currentUri()->withPort(null)->withPath('')->withQuery('')->withFragment('');
        if ($this->enabled && !empty($this->getCdnDomains())) {
            $htaccessContents = <<<APACHECONFIG
<IfModule mod_headers.c>
    Header append Access-Control-Allow-Origin "{$origin}"
    Header append Vary "Origin"
</IfModule>
APACHECONFIG;
            try {
                \JchOptimize\Core\Htaccess::updateHtaccess($htaccessContents, $htaccessDelimiters, 'append');
            } catch (Exception $e) {
            }
        } else {
            \JchOptimize\Core\Htaccess::cleanHtaccess($htaccessDelimiters);
        }
    }
    /**
     * @param array<string, array{domain:UriInterface, extensions:string}> $domainArray
     * @param UriInterface $uri
     * @return UriInterface
     */
    private function selectDomain(array &$domainArray, UriInterface $uri): UriInterface
    {
        //If no domain is matched to a configured file type then we'll just return the file
        $cdnUri = new Uri();
        for ($i = 0; count($domainArray) > $i; $i++) {
            $domain = current($domainArray);
            $staticFiles = $domain['extensions'];
            next($domainArray);
            if (current($domainArray) === \false) {
                reset($domainArray);
            }
            if (preg_match('#\\.(?>' . $staticFiles . ')#i', $uri->getPath())) {
                //Prepend the cdn domain to the file path if a match is found.
                $cdnDomain = $domain['domain'];
                //Some CDNs like Cloudinary includes path to the CDN domain to be prepended to the asset
                $uri = $uri->withPath(\rtrim($cdnDomain->getPath(), '/') . '/' . \ltrim($uri->getPath(), '/'));
                $cdnUri = UriResolver::resolve($cdnDomain, UriConverter::absToNetworkPathReference($uri));
                break;
            }
        }
        return $cdnUri;
    }
}
