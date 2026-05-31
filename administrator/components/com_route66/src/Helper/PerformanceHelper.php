<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Helper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Firecoders\Component\Route66\Administrator\Optimizer\CSSOptimizer;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;

class PerformanceHelper
{
    public static function optimize(): void
    {
        $application = Factory::getApplication();

        if (!$application->isClient('site')) {
            return;
        }

        if ($application->input->getMethod() !== 'GET') {
            return;
        }

        $document = Factory::getDocument();

        if ($document->getType() !== 'html') {
            return;
        }

        $params = ComponentHelper::getParams('com_route66');

        if (!$params->get('iframe_facades') && !$params->get('iframes_lazy_load') && !$params->get('images_lazy_load') && !$params->get('optimize_css')) {
            return;
        }

        $buffer = '<?xml encoding="UTF-8">' . $application->getBody();

        libxml_use_internal_errors(true);
        $dom                     = new \DOMDocument('1.0', 'UTF-8');
        $dom->preserveWhiteSpace = false;
        $dom->formatOutput       = false;
        $dom->loadHTML($buffer, LIBXML_SCHEMA_CREATE);

        if ($params->get('iframe_facades')) {
            self::facadeIframes($dom);
        }

        if ($params->get('iframes_lazy_load')) {
            self::lazyLoadIframes($dom);
        }

        if ($params->get('images_lazy_load')) {
            self::lazyLoadImages($dom);
        }

        if ($params->get('optimize_css')) {
            self::optimizeStyles($dom);
        }

        $buffer = $dom->saveHTML();
        $buffer = str_replace('<?xml encoding="UTF-8">', '', $buffer);
        $buffer = html_entity_decode($buffer, ENT_HTML5, 'UTF-8');

        $application->setBody($buffer);
    }

    public static function assets(): void
    {
        $application = Factory::getApplication();

        if (!$application->isClient('site')) {
            return;
        }

        if ($application->input->getMethod() !== 'GET') {
            return;
        }

        $document = Factory::getDocument();

        if ($document->getType() !== 'html') {
            return;
        }

        $params = ComponentHelper::getParams('com_route66');

        if (!$params->get('iframe_facades')) {
            return;
        }

        $wa = $document->getWebAssetManager();
        $wa->registerAndUseScript('route66.lite-youtube', 'route66/lite-youtube/lite-youtube.min.js', [], ['type' => 'module']);
        $wa->registerAndUseScript('route66.lite-vimeo', 'route66/lite-vimeo/lite-vimeo.min.js', [], ['type' => 'module']);
    }


    protected static function lazyLoadImages(\DomDocument $document): void
    {
        $params = ComponentHelper::getParams('com_route66');

        $mode      = $params->get('images_lazy_load_mode');
        $className = $params->get('images_lazy_load_classname');

        $images = $document->getElementsByTagName('img');

        for ($i = $images->length; --$i >= 0;) {

            $image = $images->item($i);

            if ($mode && $className) {

                $class   = $image->getAttribute('class');
                $classes = explode(' ', $class);
                $classes = array_filter($classes);

                if ($mode === 'inclusive' && !\in_array($className, $classes)) {
                    continue;
                } elseif ($mode === 'exclusive' && \in_array($className, $classes)) {
                    continue;
                }
            }

            $image->setAttribute('loading', 'lazy');
        }
    }

    protected static function lazyLoadIframes(\DomDocument $document): void
    {
        $params = ComponentHelper::getParams('com_route66');

        $mode      = $params->get('iframes_lazy_load_mode');
        $className = $params->get('iframes_lazy_load_classname');

        $iframes = $document->getElementsByTagName('iframe');

        for ($i = $iframes->length; --$i >= 0;) {

            $iframe = $iframes->item($i);

            if ($mode && $className) {

                $class   = $iframe->getAttribute('class');
                $classes = explode(' ', $class);
                $classes = array_filter($classes);

                if ($mode === 'inclusive' && !\in_array($className, $classes)) {
                    continue;
                } elseif ($mode === 'exclusive' && \in_array($className, $classes)) {
                    continue;
                }
            }

            $iframe->setAttribute('loading', 'lazy');
        }
    }

    protected static function facadeIframes(\DomDocument $document)
    {
        $params = ComponentHelper::getParams('com_route66');

        $mode      = $params->get('iframe_facades_mode');
        $className = $params->get('iframe_facades_classname');

        $iframes = $document->getElementsByTagName('iframe');

        for ($i = $iframes->length; --$i >= 0;) {

            $iframe = $iframes->item($i);

            if ($mode && $className) {

                $class   = $iframe->getAttribute('class');
                $classes = explode(' ', $class);
                $classes = array_filter($classes);

                if ($mode === 'inclusive' && !\in_array($className, $classes)) {
                    continue;
                } elseif ($mode === 'exclusive' && \in_array($className, $classes)) {
                    continue;
                }
            }

            $src = $iframe->getAttribute('src');

            if (str_contains($src, '//www.youtube.com/embed/') || strpos($src, '//www.youtube-nocookie.com/embed/')) {
                self::setYoutubeFacade($document, $iframe);
            } elseif (str_contains($src, '//player.vimeo.com/video/')) {
                self::setVimeoFacade($document, $iframe);
            }
        }
    }

    protected static function setYoutubeFacade(\DomDocument $document, \DomElement $iframe)
    {
        $src = $iframe->getAttribute('src');

        $parsed = parse_url($src);
        $host   = $parsed['host'];
        $path   = $parsed['path'];
        parse_str($parsed['query'], $query);

        $parts   = array_values(array_filter(explode('/', $path)));
        $videoId = array_pop($parts);

        if (!$videoId) {
            return;
        }

        if ($videoId === 'videoseries') {
            return;
        }

        $video = $document->createElement('lite-youtube');
        $video->setAttribute('videoid', $videoId);

        $playlistId = $query['list'] ?? '';
        if ($playlistId) {
            $video->setAttribute('playlistid', $playlistId);
        }

        $start = $query['start'] ?? 0;
        if ($start) {
            $video->setAttribute('videoStartAt', $start);
        }

        $title = $iframe->getAttribute('title');
        if ($title) {
            $video->setAttribute('videotitle', $title);
        }

        if ($host === 'www.youtube-nocookie.com') {
            $video->setAttribute('nocookie', true);
        }

        $iframe->parentNode->replaceChild($video, $iframe);
    }

    protected static function setVimeoFacade(\DomDocument $document, \DomElement $iframe)
    {
        $src = $iframe->getAttribute('src');

        $parsed = parse_url($src);
        $path   = $parsed['path'];
        $parts  = array_values(array_filter(explode('/', $path)));

        $prefix = $parts[0] ?? '';

        if ($prefix !== 'video') {
            return;
        }

        $videoId = $parts[1] ?? '';

        if (!$videoId) {
            return;
        }

        $video = $document->createElement('lite-vimeo');
        $video->setAttribute('videoid', $videoId);

        $title = $iframe->getAttribute('title');
        if ($title) {
            $video->setAttribute('videotitle', $title);
        }

        $iframe->parentNode->replaceChild($video, $iframe);
    }

    protected static function optimizeStyles(\DomDocument $document)
    {
        $xpath  = new \DOMXpath($document);
        $styles = $xpath->query('//link[@rel="stylesheet"] | //style');

        $optimizer = new CSSOptimizer();
        $processed = [];

        foreach ($styles as $key => $style) {
            $result = $optimizer->add($style);
            if ($result) {
                $processed[] = $key;
            }
        }

        $css = $optimizer->combine();

        if (!$css) {
            return;
        }

        foreach ($styles as $key => $style) {
            if (\in_array($key, $processed)) {
                $style->parentNode->removeChild($style);
            }
        }

        $head  = $document->getElementsByTagName('head')->item(0);
        $style = $document->createElement('style', $css);
        $head->appendChild($style);
    }
}
