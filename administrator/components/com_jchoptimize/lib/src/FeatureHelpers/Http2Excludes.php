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

namespace JchOptimize\Core\FeatureHelpers;

use JchOptimize\Core\Helper;
use JchOptimize\Core\Http2Preload;
use JchOptimize\Core\Registry;
use JchOptimize\Core\Uri\Utils;
use Joomla\DI\Container;
use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

use function defined;
use function pathinfo;

use const PATHINFO_EXTENSION;

defined('_JCH_EXEC') or die('Restricted access');
class Http2Excludes extends \JchOptimize\Core\FeatureHelpers\AbstractFeatureHelper
{
    /**
     * @var Http2Preload
     */
    private Http2Preload $http2Preload;
    public function __construct(Container $container, Registry $params, Http2Preload $http2Preload)
    {
        parent::__construct($container, $params);
        $this->http2Preload = $http2Preload;
    }
    public function addHttp2Includes(): void
    {
        if (!$this->http2Preload->isEnabled()) {
            return;
        }
        /** @var string[] $includeFiles */
        $includeFiles = $this->params->get('pro_http2_include', []);
        if (empty($includeFiles)) {
            return;
        }
        foreach ($includeFiles as $includeFile) {
            $extension = \strtolower(pathinfo($includeFile, PATHINFO_EXTENSION));
            $type = match ($extension) {
                'js' => 'script',
                'css' => 'style',
                'woff', 'woff2', 'ttf' => 'font',
                'webp', 'gif', 'jpg', 'jpeg', 'png' => 'image',
                default => '',
            };
            if ($type) {
                $this->http2Preload->addAdditional(Utils::uriFor($includeFile), $type, $extension);
            }
        }
    }
    public function findHttp2Excludes(UriInterface $uri): bool
    {
        if (Helper::findExcludes($this->params->get('pro_http2_exclude', []), (string) $uri)) {
            return \true;
        }
        return \false;
    }
}
