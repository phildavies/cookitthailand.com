<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Crawler;

use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class Profile extends CrawlInternalUrls
{
    public function shouldCrawl(UriInterface $url): bool
    {
        if (!parent::shouldCrawl($url)) {
            return false;
        }

        return true;
    }
}
