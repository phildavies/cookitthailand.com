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

namespace JchOptimize\Core\FeatureHelpers;

use JchOptimize\Core\Admin\Helper;
use JchOptimize\Core\Cdn;
use JchOptimize\Core\Html\Elements\Img;
use JchOptimize\Core\Html\HtmlElementInterface;
use JchOptimize\Core\Uri\UriConverter;
use JchOptimize\Platform\Paths;
use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

use function array_map;
use function file_exists;
use function implode;
use function pathinfo;

class ResponsiveImages extends \JchOptimize\Core\FeatureHelpers\AbstractFeatureHelper
{
    public static array $breakpoints = ['576', '768'];
    public static string $sizes = "(min-resolution: 3dppx) 25vw, (min-resolution: 2dppx) 30vw, (min-resolution: 1dppx) 50vw, 100vw";
    public function convert(HtmlElementInterface $element): void
    {
        if (!$element instanceof Img) {
            return;
        }
        /** @var string $width */
        $width = ($element->getWidth() ?: $element->attributeValue('data-width')) ?: 0;
        if ($element->getSrc() instanceof UriInterface && (int) $width > 1 && !$element->hasAttribute('srcset')) {
            $this->makeResponsiveImages($element);
        }
    }
    private function makeResponsiveImages(Img $element): void
    {
        $srcsetString = $this->createSrcsetString($this->getResponsiveImages($element->getSrc()), $element->getSrc(), $element->getWidth() ?: $element->attributeValue('data-width'));
        if ($srcsetString) {
            $element->srcset($srcsetString);
            $element->sizes(self::$sizes);
        }
    }
    public function createSrcsetString(array $rsImages, UriInterface $uri, string $width = ''): string
    {
        $srcset = array_map(fn(string $breakpoint, string $image): string => $image . ' ' . $breakpoint . 'w', \array_keys($rsImages), \array_values($rsImages));
        if (!empty($srcset)) {
            //If responsive images found we add the original as fallback
            $src = (string) $uri;
            $width = $width ?: $this->getImageSizeFromUri($uri);
            $srcset[] = $src . ' ' . $width . 'w';
        }
        return implode(', ', $srcset);
    }
    private function getImageSizeFromUri(UriInterface $uri): string
    {
        $imagePath = UriConverter::uriToFilePath($uri);
        $size = \getimagesize($imagePath);
        return $size[0] ?? '1';
    }
    public function getResponsiveImages(UriInterface $image): array
    {
        $imageName = $this->getResponseImageName($image);
        return $this->getResponsiveImagesArray($imageName);
    }
    private function getResponsiveImagesArray(string $rsImageName): array
    {
        $rsImages = [];
        foreach (self::$breakpoints as $breakpoint) {
            $rsImagePath = '/' . $breakpoint . '/' . $rsImageName;
            $potentialPaths = [];
            if ($this->params->get('pro_load_webp_images', '0')) {
                $fileParts = pathinfo($rsImagePath);
                $potentialPaths[] = $fileParts['dirname'] . '/' . $fileParts['filename'] . '.webp';
            }
            $potentialPaths[] = $rsImagePath;
            foreach ($potentialPaths as $potentialPath) {
                $filePath = Paths::responsiveImagePath() . $potentialPath;
                if (file_exists($filePath)) {
                    $rsImages[$breakpoint] = (string) $this->pathToUrlResponsive($filePath);
                    break;
                }
            }
        }
        return $rsImages;
    }
    private function pathToUrlResponsive(string $path): UriInterface
    {
        $cdn = $this->getContainer()->get(Cdn::class);
        $uri = UriConverter::filePathToUri($path, $cdn);
        if (!$cdn->isFileOnCdn($uri)) {
            return UriConverter::absToNetworkPathReference($uri);
        }
        return $uri;
    }
    public function getResponseImageName(UriInterface $image): string
    {
        $imagePath = Helper::contractFileName(UriConverter::uriToFilePath($image));
        return \rawurldecode($imagePath);
    }
}
