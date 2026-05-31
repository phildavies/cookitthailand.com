<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Helper;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\SiteRouter;
use Joomla\CMS\Uri\Uri;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class UriHelper
{
    public static function getLink($uri): string
    {
        $path  = $uri->getPath();
        $path  = rawurldecode($path);
        $link  = $path;
        $link  = strtolower($link);
        $link  = trim($link);

        return $link;
    }

    public static function parse(string $uri): array
    {
        $application = Factory::getApplication();

        // Router parse through API only works in front-end at the moment. Fallback to custom endpoint for other cases
        try {
            $query = $application->isClient('site') ? self::parseApi($uri) : self::parseRequest($uri);
        } catch (\Throwable $th) {
            $query = [];
        }

        return $query;
    }

    protected static function parseApi(string $url)
    {
        $uri    = Uri::getInstance($url);
        $router = Factory::getContainer()->get(SiteRouter::class);

        // Fix for router redirect when force SSL is used
        if (Factory::getApplication()->get('force_ssl') == 2) {
            $router->detachRule('parse', [$router, 'parseCheckSSL'], SiteRouter::PROCESS_BEFORE);
        }

        $router->parse($uri);
        $query = $uri->getQuery(true);

        return $query;
    }

    protected static function parseRequest(string $url)
    {
        $params  = ComponentHelper::getParams('com_route66');
        $siteUrl = $params->get('site_url');

        $http = HttpFactory::getHttp();

        $endpoint = rtrim($siteUrl, '/').'/index.php?option=com_route66&task=uri.parse&format=json';

        $payload         = ['uri' => $url];
        $payload['hash'] = HashHelper::generateHash($payload);

        $data = $http->post($endpoint, json_encode($payload), ['Content-Type' => 'application/json'], 5);

        if ($data->code < 200 || $data->code >= 300) {
            throw new \Exception(Text::_('COM_ROUTE66_ERROR_PARSE_FAILED'));
        }

        $body = json_decode($data->body);

        if (!isset($body->query)) {
            return [];
        }

        $query = $body->query;

        if (!\is_object($query)) {
            return [];
        }

        return (array) $query;
    }


}
