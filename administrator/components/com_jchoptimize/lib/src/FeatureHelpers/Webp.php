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

use JchOptimize\Core\Admin\Helper as AdminHelper;
use JchOptimize\Core\Browser;
use JchOptimize\Core\Cdn;
use JchOptimize\Core\Css\Parser;
use JchOptimize\Core\Helper;
use JchOptimize\Core\Html\Elements\BaseElement;
use JchOptimize\Core\Html\Elements\Img;
use JchOptimize\Core\Html\Elements\Input;
use JchOptimize\Core\Html\Elements\Source;
use JchOptimize\Core\Html\HtmlElementInterface;
use JchOptimize\Core\Uri\UriConverter;
use JchOptimize\Core\Uri\Utils;
use JchOptimize\Platform\Paths;
use Joomla\Filesystem\Folder;
use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

use function array_map;
use function defined;
use function file_exists;
use function pathinfo;
use function preg_replace_callback;
use function rawurldecode;
use function str_replace;

defined('_JCH_EXEC') or die('Restricted access');
class Webp extends \JchOptimize\Core\FeatureHelpers\AbstractFeatureHelper
{
    private bool $testRunning = \false;
    /**
     * @param BaseElement $element
     * @return void
     */
    public function convert(HtmlElementInterface $element): void
    {
        if ($element instanceof Img || $element instanceof Input) {
            if ($element->getSrc() instanceof UriInterface) {
                $this->processSrcAttribute($element);
            }
            if ($element instanceof Img && $element->hasAttribute('srcset')) {
                $this->processSrcSetAttribute($element);
            }
        } elseif ($element instanceof Source && $element->hasAttribute('srcset') && !$element->hasAttribute('type')) {
            $this->processSrcSetAttribute($element);
        } elseif ($element->getStyle() !== \false) {
            $this->processStyleAttribute($element);
        }
    }
    private function processSrcAttribute(Img|Input $element): void
    {
        $srcWebpValue = $this->getWebpImages($element->getSrc());
        $element->src($srcWebpValue);
    }
    private function processStyleAttribute(HtmlElementInterface $element): void
    {
        $style = preg_replace_callback("#" . Parser::cssUrlWithCaptureValueToken(\true) . '#i', function ($matches) {
            if (!empty($matches[1])) {
                $webp = $this->getWebpImages(Utils::uriFor($matches[1]));
                return str_replace($matches[1], (string) $webp, $matches[0]);
            }
            return $matches[0];
        }, $element->getStyle());
        $element->style($style);
    }
    private function processSrcSetAttribute(Img|Source $element): void
    {
        $srcSet = $element->getSrcset();
        $urls = Helper::extractUrlsFromSrcset($srcSet);
        $webpUrls = array_map(function (UriInterface $v) {
            return (string) $this->getWebpImages($v);
        }, $urls);
        if ($urls != $webpUrls) {
            $webpSrcSet = str_replace($urls, $webpUrls, $srcSet);
            $element->srcset($webpSrcSet);
        }
    }
    public function getWebpImages(UriInterface $imageUri): UriInterface
    {
        if ($imageUri->getScheme() == 'data' || !self::canIUse()) {
            return $imageUri;
        }
        $imagePath = UriConverter::uriToFilePath($imageUri);
        $aPotentialPaths = [self::getWebpPath($imagePath), self::getWebpPathLegacy($imagePath)];
        $cdn = $this->getContainer()->get(Cdn::class);
        foreach ($aPotentialPaths as $potentialWebpPath) {
            if ($this->fileExists($potentialWebpPath)) {
                $webpImageUri = UriConverter::filePathToUri($potentialWebpPath, $cdn);
                $webpImageUri = $webpImageUri->withQuery($imageUri->getQuery())->withFragment($imageUri->getFragment());
                if (!$cdn->isFileOnCdn($webpImageUri)) {
                    return UriConverter::absToNetworkPathReference($webpImageUri);
                }
                return $webpImageUri;
            }
        }
        return $imageUri;
    }
    public function fileExists(string $path): bool
    {
        if ($this->testRunning) {
            return \true;
        }
        return @file_exists($path);
    }
    /**
     * Tries to determine if client supports WEBP images based on https://caniuse.com/webp
     */
    protected static function canIUse(): bool
    {
        $browser = Browser::getInstance();
        $browserName = $browser->getBrowser();
        //WEBP only supported in Safari running on MacOS 11 or higher, best to avoid.
        if ($browserName == 'Internet Explorer' || $browserName == 'Safari') {
            return \false;
        }
        return \true;
    }
    /**
     * @param string $originalImagePath
     * @return string
     */
    public static function getWebpPathLegacy(string $originalImagePath): string
    {
        if (!file_exists(Paths::nextGenImagesPath())) {
            Folder::create(Paths::nextGenImagesPath());
        }
        $fileParts = pathinfo(AdminHelper::contractFileNameLegacy($originalImagePath));
        return Paths::nextGenImagesPath() . '/' . $fileParts['filename'] . '.webp';
    }
    /**
     * @param string $originalImagePath
     * @return string
     */
    public static function getWebpPath(string $originalImagePath): string
    {
        if (!file_exists(Paths::nextGenImagesPath())) {
            Folder::create(Paths::nextGenImagesPath());
        }
        $fileParts = pathinfo(AdminHelper::contractFileName($originalImagePath));
        return Paths::nextGenImagesPath() . '/' . rawurldecode($fileParts['filename']) . '.webp';
    }
    public function enableTestRunning(): void
    {
        $this->testRunning = \true;
    }
}
