<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Helper;

use GuzzleHttp\RequestOptions;
use Joomla\CMS\Date\Date;
use Spatie\Robots\RobotsTxt;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class CrawlerHelper
{
    public static function getOptions()
    {
        $encodings = ['gzip', 'deflate'];

        $curl = curl_version();

        if (!empty($curl['brotli_version'])) {
            $encodings[] = 'br';
        }

        if (!empty($curl['zstd_version'])) {
            $encodings[] = 'zstd';
        }

        $options = [
            RequestOptions::COOKIES         => false,
            RequestOptions::CONNECT_TIMEOUT => 10,
            RequestOptions::TIMEOUT         => 10,
            RequestOptions::ALLOW_REDIRECTS => false,
            RequestOptions::HEADERS         => [
                'Accept-Encoding' => implode(',', $encodings),
            ],
        ];

        return $options;
    }

    public static function getRobotsTxt()
    {
        $robotsTxt = null;

        if (file_exists(JPATH_SITE.'/robots.txt')) {
            $robotsTxt = new RobotsTxt(file_get_contents(JPATH_SITE.'/robots.txt'));
        }

        return $robotsTxt;
    }

    public static function isRunning($task)
    {
        $now     = new Date();
        $updated = new Date($task->modified);

        $seconds = $now->getTimestamp() - $updated->getTimestamp();
        $minutes = $seconds / 60;

        return $minutes <= 5;
    }

    public static function isIncomplete($task)
    {
        return !CrawlerHelper::isRunning($task);
    }
}
