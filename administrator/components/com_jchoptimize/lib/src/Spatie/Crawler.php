<?php

namespace JchOptimize\Core\Spatie;

use _JchOptimizeVendor\GuzzleHttp\RequestOptions;
use _JchOptimizeVendor\Spatie\Crawler\Crawler as SpatieCrawler;
use _JchOptimizeVendor\Spatie\Crawler\CrawlProfiles\CrawlInternalUrls;

class Crawler
{
    public static function create($baseUrl): SpatieCrawler
    {
        $clientOptions = [RequestOptions::COOKIES => \false, RequestOptions::CONNECT_TIMEOUT => 100, RequestOptions::TIMEOUT => 100, RequestOptions::ALLOW_REDIRECTS => \true, RequestOptions::HEADERS => ['User-Agent' => '*']];
        return SpatieCrawler::create($clientOptions)->setParseableMimeTypes(['text/html'])->ignoreRobots()->setCrawlProfile(new CrawlInternalUrls($baseUrl));
    }
}
