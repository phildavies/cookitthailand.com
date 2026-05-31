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

use CodeAlfa\Minify\Css;
use JchOptimize\Core\Css\Callbacks\CorrectUrls;
use JchOptimize\Core\Css\Parser as CssParser;
use JchOptimize\Core\Helper;
use JchOptimize\Core\Html\ElementObject;
use JchOptimize\Core\Html\Elements\Audio;
use JchOptimize\Core\Html\Elements\Video;
use JchOptimize\Core\Html\HtmlElementInterface;
use JchOptimize\Core\Html\HtmlManager;
use JchOptimize\Core\Html\Parser;
use JchOptimize\Core\Html\Processor;
use JchOptimize\Core\Registry;
use JchOptimize\Core\SystemUri;
use JchOptimize\Core\Uri\Utils;
use JchOptimize\Platform\Paths;
use Joomla\DI\Container;
use _JchOptimizeVendor\Laminas\EventManager\Event;
use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

use function defined;
use function json_encode;
use function preg_match;
use function preg_replace;
use function preg_replace_callback;
use function str_replace;

defined('_JCH_EXEC') or die('Restricted access');
class LazyLoadExtended extends \JchOptimize\Core\FeatureHelpers\AbstractFeatureHelper
{
    /**
     * @var array $cssBgImagesSelectors The selectors for CSS rules found with background images
     */
    public array $cssBgImagesSelectors = [];
    /**
     * @var HtmlManager
     */
    private HtmlManager $htmlManager;
    public function __construct(Container $container, Registry $params, HtmlManager $htmlManager)
    {
        parent::__construct($container, $params);
        $this->htmlManager = $htmlManager;
    }
    public static function lazyLoadAudioVideo(Audio|Video $element): void
    {
        if ($element instanceof Video) {
            $poster = $element->getPoster();
            if ($poster instanceof UriInterface) {
                //If poster value invalid just remove it
                if ((string) $poster != '' && $poster->getPath() != SystemUri::basePath()) {
                    $element->data('poster', $poster);
                }
                $element->remove('poster');
            }
        }
        if ($element->hasAttribute('autoplay')) {
            $element->data('autoplay');
            $element->remove('autoplay');
        }
        $element->preload('none');
        $element->class('jch-lazyload');
    }
    public static function lazyLoadBgImages(HtmlElementInterface $element): void
    {
        if ($element->hasAttribute('style')) {
            $style = $element->getStyle();
            $style = preg_replace_callback('#(?:background-image\\s*+:\\s*+)?' . CssParser::cssUrlWithCaptureValueToken(\true) . '(?:\\s*+;\\s*+)?#i', function ($match) use ($element) {
                if (isset($match[1])) {
                    $element->data('bg', $match[1]);
                    $element->class('jch-lazyload');
                    return '';
                }
                return $match[0];
            }, $style);
            $element->style($style);
        }
    }
    public static function getLazyLoadClass($aMatches)
    {
        return $aMatches[4];
    }
    public function setupLazyLoadExtended(Parser $parser, $section): void
    {
        if ($section == 'below_fold' && $this->params->get('pro_lazyload_iframe', '0')) {
            $iFrameElement = new ElementObject();
            $iFrameElement->setNamesArray(['iframe']);
            $iFrameElement->setCaptureAttributesArray(['class', 'src']);
            $parser->addElementObject($iFrameElement);
            unset($iFrameElement);
        }
        if ($section == 'above_fold' && $this->params->get('pro_lcp_images_enable', '0') || $section == 'below_fold' && $this->params->get('pro_lazyload_bgimages', '0') || $this->params->get('pro_next_gen_images', '1')) {
            $bgElement = new ElementObject();
            $bgElement->setNamesArray(['[^\\s/"\'=<>]++']);
            $bgElement->bSelfClosing = \true;
            $bgElement->setCaptureAttributesArray(['class', 'style']);
            //language=RegExp
            $sValueCriteriaRegex = '(?=(?>[^b>]*+b?)*?[^b>]*+(background(?:-image)?))' . '(?=(?>[^u>]*+u?)*?[^u>]*+(' . CssParser::cssUrlWithCaptureValueToken(\true) . '))';
            $bgElement->setValueCriteriaRegex(['style' => $sValueCriteriaRegex]);
            $parser->addElementObject($bgElement);
            unset($bgElement);
            $styleElement = new ElementObject();
            $styleElement->setNamesArray(['style']);
            $styleElement->addNegAttrCriteriaRegex('id==[\'"]?jch-optimize-critical-css[\'"]?');
            $styleElement->bCaptureContent = \true;
            $parser->addElementObject($styleElement);
            unset($styleElement);
        }
        if ($section == 'above_fold' && $this->params->get('pro_lcp_images_enable', '0') || $section == 'below_fold' && $this->params->get('pro_lazyload_audiovideo', '0')) {
            $audioVideoElement = new ElementObject();
            $audioVideoElement->setNamesArray(['video', 'audio']);
            $audioVideoElement->setCaptureAttributesArray(['class', 'src', 'poster', 'preload', 'autoplay']);
            $parser->addElementObject($audioVideoElement);
            unset($audioVideoElement);
        }
    }
    public function lazyLoadCssBackgroundImages(Event $event): void
    {
        if ($this->params->get('lazyload_enable', '0') && $this->params->get('pro_lazyload_bgimages', '0') && !empty($this->cssBgImagesSelectors)) {
            $cssSelectors = \array_unique($this->cssBgImagesSelectors);
            $jsSelectorsArray = [];
            foreach ($cssSelectors as $cssSelector) {
                $jsSelectorsArray[] = [$cssSelector, Helper::cssSelectorsToClass($cssSelector)];
            }
            $jsSelectors = json_encode($jsSelectorsArray);
            $script = <<<HTML
<script>
document.addEventListener("DOMContentLoaded", (event) => {
    jchLazyLoadBgImages();
});
document.addEventListener("onJchDomLoaded", (event) => {
    jchLazyLoadBgImages();
});
function jchLazyLoadBgImages(){
    const selectors = {$jsSelectors};

    selectors.forEach(function(selectorPair){
        let elements = document.querySelectorAll(selectorPair[0])
    
        elements.forEach((element) => {
            if (element && !element.classList.contains(selectorPair[1])){
                element.classList.add(selectorPair[1],  'jch-lazyload');
            }
        });    
    });  
}
</script>
HTML;
            $this->htmlManager->appendChildToHTML($script, 'body');
        }
    }
    public function addCssLazyLoadAssetsToHtml(Event $event): void
    {
        /** @var Processor $htmlProcessor */
        $htmlProcessor = $this->getContainer()->get(Processor::class);
        if (JCH_PRO && $this->params->get('lazyload_enable', '0') && !$htmlProcessor->isAmpPage) {
            if ($this->params->get('pro_lazyload_effects', '0')) {
                $url = Paths::mediaUrl() . '/core/css/ls.effects.css?' . JCH_VERSION;
                $link = $this->htmlManager->getNewCssLink($url);
                $this->htmlManager->appendChildToHead($link);
            }
            $cssNoScript = <<<HTML
<noscript>
    <style>
        img.jch-lazyload, iframe.jch-lazyload{
            display: none;
        }
    </style>
</noscript>
HTML;
            $this->htmlManager->appendChildToHead($cssNoScript);
        }
    }
    public function handleCssBgImages(CorrectUrls $correctUrls, string $css): string
    {
        if (preg_match("#" . CssParser::cssRuleWithCaptureValueToken(\true) . '#i', $css, $ruleMatches)) {
            //Make sure we're not lazyloading any URL that was commented out
            $cleanedCss = preg_replace('#' . CssParser::blockCommentToken() . '#', '', $ruleMatches[2]);
            if (preg_match('#background(?:-image)?\\s*+:\\s*+\\K' . CssParser::cssUrlWithCaptureValueToken(\true) . '#', $cleanedCss, $urlMatches)) {
                $cssUri = Utils::uriFor($urlMatches[1]);
                //Exclude LCP images
                if (Helper::findMatches(Helper::getArray($this->params->get('pro_lcp_images', [])), $cssUri)) {
                    return $css;
                }
                //Don't need to lazy-load data-image
                if ($this->params->get('pro_lazyload_bgimages', '0') && $cssUri->getScheme() != 'data' && !Helper::findExcludes(Helper::getArray($this->params->get('excludeLazyLoad', [])), (string) $cssUri) && !Helper::findExcludes(Helper::getArray($this->params->get('pro_excludeLazyLoadFolders', [])), (string) $cssUri)) {
                    //Remove the background image
                    $ruleMatches[0] = str_replace($urlMatches[0], '', $ruleMatches[0]);
                    //Remove any empty background declarations
                    $ruleMatches[0] = preg_replace('#background(?:-image)?\\s*+:\\s*+(?:!important)?\\s*+(?:;|(?=}))#', '', $ruleMatches[0]);
                    //Add the lazy-loaded image to CSS
                    $modifiedCss = $ruleMatches[0] . '.' . Helper::cssSelectorsToClass($ruleMatches[1]) . '.jch-lazyloaded{background-image:' . $urlMatches[0] . ' !important}';
                    //Save the selector for this rule
                    $correctUrls->cssBgImagesSelectors[] = Css::optimize($ruleMatches[1]);
                    $this->cssBgImagesSelectors[] = Css::optimize($ruleMatches[1]);
                    return $modifiedCss;
                }
            }
        }
        return $css;
    }
}
