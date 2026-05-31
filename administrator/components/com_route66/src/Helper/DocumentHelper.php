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

use Joomla\CMS\Language\Text;

class DocumentHelper
{
    public static function getPageData(string $html, array $headers)
    {
        $dom = new \DomDocument('1.0', 'UTF-8');
        libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML($html);

        if (!$loaded) {
            throw new \RuntimeException(Text::_('COM_ROUTE66_COULD_NOT_PARSE_HTML'), 500);
        }

        libxml_clear_errors();

        // Title
        $title = $dom->getElementsByTagName('title')->item(0)->nodeValue;
        $title = trim($title);

        $xpath = new \DOMXPath($dom);

        // Meta Description
        $description = null;

        $meta = $xpath->query('//meta[@name="description"]');
        if ($meta->length > 0) {
            $description = $meta->item(0)->getAttribute('content');
            $description = trim($description);
        }

        // Robots
        $robots = null;

        $meta = $xpath->query('//meta[@name="robots"]');
        if ($meta->length > 0) {
            $robots = $meta->item(0)->getAttribute('content');
            $robots = mb_strtolower(trim($robots));
            $parts  = explode(',', $robots);
            sort($parts, SORT_STRING);
            $robots = implode(',', $parts);
        }

        // Canonical
        $canonical = null;

        $meta = $xpath->query('//link[@rel="canonical"]');
        if ($meta->length > 0) {
            $canonical = $meta->item(0)->getAttribute('href');
            $canonical = trim($canonical);
        }

        // Language
        $language = null;

        $root = $dom->getElementsByTagName('html')->item(0);
        if ($root && $root->getAttribute('lang')) {
            $language = $root->getAttribute('lang');
            $language = mb_strtolower(trim($language));
        }

        // DOM nodes
        $nodes = $xpath->query('//*')->length;

        // Content encoding
        $encoding = null;

        $encodingHeaders = ['Content-Encoding', 'content-encoding', 'x-encoded-content-encoding'];
        foreach ($encodingHeaders as $encodingHeader) {
            if (isset($headers[$encodingHeader])) {
                $encoding = $headers[$encodingHeader][0];
                $encoding = trim(mb_strtolower($encoding));
                break;
            }
        }

        // Size
        $size = \strlen($html);

        return [$title, $description, $robots, $language, $encoding, $nodes, $size, $canonical];
    }
}
