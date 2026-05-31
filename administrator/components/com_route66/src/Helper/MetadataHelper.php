<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Helper;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Document\HtmlDocument;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\MediaHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class MetadataHelper
{
    public static function setMetadata(): void
    {
        self::setDefaultOpenGraphImage();
        self::setCustomMetaTags();
        self::setCanonical();

        $uri         = Uri::getInstance();
        $link        = UriHelper::getLink($uri);
        $resourceId  = PageHelper::getResourceId();

        $application = Factory::getApplication();
        $model       = $application->bootComponent('com_route66')->getMVCFactory()->createModel('Metadata', 'Administrator', ['ignore_request' => true]);
        $model->setState('filter.resource_id', $resourceId);
        $model->setState('filter.link_hash', PageHelper::hash($link));
        $metadata = $model->getItem();

        if (!$metadata) {
            return;
        }

        $document = $application->getDocument();

        if ($metadata->title) {
            $document->setTitle(TitleHelper::addSiteName($metadata->title));
        }

        if ($metadata->description) {
            $document->setDescription($metadata->description);
        }

        if ($metadata->robots) {
            $document->setMetadata('robots', $metadata->robots);
        }

        if ($metadata->canonical) {
            $document->addHeadLink(htmlspecialchars($metadata->canonical), 'canonical');
        }

        if ($metadata->og_title) {
            $document->setMetadata('og:title', $metadata->og_title, 'property');
        } elseif ($metadata->title) {
            $document->setMetadata('og:title', $metadata->title, 'property');
        }

        if ($metadata->og_description) {
            $document->setMetadata('og:description', $metadata->og_description, 'property');
        } elseif ($metadata->description) {
            $document->setMetadata('og:description', $metadata->description, 'property');
        }

        if ($metadata->og_image) {
            $document->setMetadata('og:image', Uri::root(false).MediaHelper::getCleanMediaFieldValue($metadata->og_image), 'property');
        }

        if ($metadata->og_type) {
            $document->setMetadata('og:type', $metadata->og_type, 'property');
        }

        $url = $metadata->canonical ? $metadata->canonical : self::getCanonical($document);

        if (!$url) {
            $url = Uri::current();
        }

        $document->setMetadata('og:url', htmlspecialchars($url), 'property');

        $document->setMetadata('og:site_name', $application->get('sitename'), 'property');

        $params = ComponentHelper::getParams('com_route66');

        if ($params->get('facebook_page_url')) {
            $document->setMetadata('article:publisher', $params->get('facebook_page_url'), 'property');
        }

        if ($params->get('facebook_app_id')) {
            $document->setMetadata('fb:app_id', $params->get('facebook_app_id'), 'property');
        }

        if ($metadata->x_title) {
            $document->setMetadata('twitter:title', $metadata->x_title);
        }

        if ($metadata->x_description) {
            $document->setMetadata('twitter:description', $metadata->x_description);
        }

        if ($metadata->x_image) {
            $document->setMetadata('twitter:image', Uri::root(false).MediaHelper::getCleanMediaFieldValue($metadata->x_image));
        }

        if ($metadata->og_title || $metadata->x_title) {

            $document->setMetadata('twitter:card', 'summary_large_image');

            if ($params->get('x_username')) {
                $document->setMetadata('twitter:site', $params->get('x_username'));
            }
        }

    }

    public static function getCanonical(HtmlDocument $document): string
    {
        $canonical = '';

        foreach ($document->_links as $linkUrl => $link) {
            if (isset($link['relation']) && $link['relation'] === 'canonical') {
                $canonical = $linkUrl;
                break;
            }
        }

        return $canonical;
    }

    public static function setCanonical(): void
    {
        $application = Factory::getApplication();
        $params      = ComponentHelper::getParams('com_route66');

        if (!$params->get('canonical_urls')) {
            return;
        }

        $exclusions = $params->get('canonical_urls_exclusions', []);
        $option     = $application->input->getCmd('option');

        if (\is_array($exclusions) && \in_array($option, $exclusions)) {
            return;
        }

        $uri    = Uri::getInstance();
        $router = $application->getRouter();

        $vars = array_merge($router->getVars(), $uri->getQuery(true));
        $vars = array_filter($vars);

        $url       = Route::_('index.php?' . http_build_query($vars), true, Route::TLS_IGNORE, true);
        $canonical = Uri::getInstance($url)->toString(['scheme', 'host', 'port', 'path']);

        $document = $application->getDocument();
        $document->addHeadLink(htmlspecialchars($canonical), 'canonical');
    }

    protected static function setDefaultOpenGraphImage(): void
    {
        $params = ComponentHelper::getParams('com_route66');

        if (!$params->get('og_image')) {
            return;
        }

        $document = Factory::getApplication()->getDocument();
        $document->setMetadata('og:image', Uri::root(false).MediaHelper::getCleanMediaFieldValue($params->get('og_image')), 'property');
    }

    protected static function setCustomMetaTags(): void
    {
        $params = ComponentHelper::getParams('com_route66');

        if (!$params->get('custom_meta_tags')) {
            return;
        }

        $document = Factory::getApplication()->getDocument();

        $metaTags = explode(PHP_EOL, $params->get('custom_meta_tags', ''));
        foreach ($metaTags as $metaTag) {
            $document->addCustomTag($metaTag);
        }
    }
}
