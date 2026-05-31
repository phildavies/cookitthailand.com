<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/core
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2024 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Core\FeatureHelpers;

use JchOptimize\Core\Helper;
use JchOptimize\Core\Html\Elements\Img;
use JchOptimize\Core\Html\Elements\Video;
use JchOptimize\Core\Http2Preload;
use JchOptimize\Core\Registry;
use Joomla\DI\Container;
use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

use function array_merge;
use function in_array;

class LCPImages extends \JchOptimize\Core\FeatureHelpers\AbstractFeatureHelper
{
    private Http2Preload $http2Preload;
    public function __construct(Container $container, Registry $params, Http2Preload $http2Preload)
    {
        parent::__construct($container, $params);
        $this->http2Preload = $http2Preload;
    }
    public function process(Img|Video $element): bool
    {
        if ($element instanceof Img) {
            return $this->processImage($element);
        } else {
            return $this->processVideo($element);
        }
    }
    private function processImage(Img $element): bool
    {
        $uris = [];
        if ($element->hasAttribute('srcset')) {
            $srcset = $element->getSrcset();
            $uris = Helper::extractUrlsFromSrcset($srcset);
        }
        if (($src = $element->getSrc()) !== \false) {
            $uris = array_merge($uris, [$src]);
        }
        $lcpImages = Helper::getArray($this->params->get('pro_lcp_images'));
        foreach ($uris as $uri) {
            if (Helper::findMatches($lcpImages, $uri)) {
                $this->highFetchPriority($element);
                return \true;
            }
        }
        $identifiers = [];
        if (($id = $element->getId()) !== \false) {
            $identifiers[] = $id;
        }
        if (($classes = $element->getClass()) !== \false) {
            $identifiers = array_merge($identifiers, $classes);
        }
        $lcpIdentifiers = Helper::getArray($this->params->get('pro_lcp_identifiers'));
        foreach ($identifiers as $identifier) {
            if (in_array($identifier, $lcpIdentifiers)) {
                $this->highFetchPriority($element);
                return \true;
            }
        }
        return \false;
    }
    private function processVideo(Video $element): bool
    {
        $poster = $element->getPoster();
        if ($poster instanceof UriInterface) {
            $lcpImages = Helper::getArray($this->params->get('pro_lcp_images'));
            if (Helper::findMatches($lcpImages, $poster)) {
                $this->http2Preload->preload($poster, 'image', '', 'high');
                return \true;
            }
        }
        return \false;
    }
    private function highFetchPriority(Img $element): void
    {
        if ($element->hasAttribute('srcset') || $element->getParent() == 'picture') {
            $element->fetchpriority('high');
        } else {
            $this->http2Preload->preload($element->getSrc(), 'image', '', 'high');
        }
        if ($element->hasAttribute('loading')) {
            $element->loading('eager');
        }
    }
}
