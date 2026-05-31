<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Crawler;

use Firecoders\Component\Route66\Administrator\Helper\PageHelper;
use Joomla\CMS\Factory;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlQueues\CrawlQueue;
use Spatie\Crawler\CrawlUrl;
use Spatie\Crawler\Exceptions\InvalidUrl;
use Spatie\Crawler\Exceptions\UrlNotFoundByIndex;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class Queue implements CrawlQueue
{
    protected array $urls        = [];
    protected array $pendingUrls = [];

    public function add(CrawlUrl $crawlUrl): CrawlQueue
    {
        if (!$this->has($crawlUrl)) {

            $url = (string) $crawlUrl->url;
            $crawlUrl->setId($url);

            $db     = Factory::getDbo();
            $query  = $db->getQuery(true);
            $query->insert($db->qn('#__route66_crawler_queue'));
            $query->columns($db->qn(['link', 'link_hash', 'state']));
            $query->values($db->q(serialize($crawlUrl)).','. $db->q(PageHelper::hash($url)) .','. $db->q(0));
            $db->setQuery($query);
            $db->execute();

        }

        return $this;
    }

    public function hasPendingUrls(): bool
    {
        $db     = Factory::getDbo();
        $query  = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from($db->qn('#__route66_crawler_queue'));
        $query->where($db->qn('state').' = 0');
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result > 0;
    }

    public function getUrlById($id): CrawlUrl
    {
        $hash = PageHelper::hash($id);

        $db     = Factory::getDbo();
        $query  = $db->getQuery(true);
        $query->select($db->qn('link'));
        $query->from($db->qn('#__route66_crawler_queue'));
        $query->where($db->qn('link_hash'). ' = '.$db->q($hash));
        $db->setQuery($query);
        $result = $db->loadResult();

        if (!$result) {
            throw new UrlNotFoundByIndex("Crawl url {$id} not found in collection.");
        }

        return unserialize($result);
    }

    public function hasAlreadyBeenProcessed(CrawlUrl $crawlUrl): bool
    {
        $hash = PageHelper::hash((string) $crawlUrl->url);

        $db     = Factory::getDbo();
        $query  = $db->getQuery(true);
        $query->select($db->qn('link'));
        $query->from($db->qn('#__route66_crawler_queue'));
        $query->where($db->qn('link_hash'). ' = '.$db->q($hash));
        $query->where($db->qn('state').' = 1');
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result ? true : false;
    }

    public function markAsProcessed(CrawlUrl $crawlUrl): void
    {
        $hash = PageHelper::hash((string) $crawlUrl->url);

        $db     = Factory::getDbo();
        $query  = $db->getQuery(true);
        $query->update($db->qn('#__route66_crawler_queue'));
        $query->set($db->qn('state').' = 1');
        $query->where($db->qn('link_hash'). ' = '.$db->q($hash));
        $db->setQuery($query);
        $db->execute();
    }

    public function getProcessedUrlCount(): int
    {
        $db     = Factory::getDbo();
        $query  = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from($db->qn('#__route66_crawler_queue'));
        $query->where($db->qn('state').' = 1');
        $db->setQuery($query);
        $result = $db->loadResult();

        return (int) $result;
    }

    public function has(CrawlUrl|UriInterface $crawlUrl): bool
    {
        if ($crawlUrl instanceof CrawlUrl) {
            $urlString = (string) $crawlUrl->url;
        } elseif ($crawlUrl instanceof UriInterface) {
            $urlString = (string) $crawlUrl;
        } else {
            throw InvalidUrl::unexpectedType($crawlUrl);
        }

        $hash = PageHelper::hash($urlString);

        $db     = Factory::getDbo();
        $query  = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from($db->qn('#__route66_crawler_queue'));
        $query->where($db->qn('link_hash').' = '.$db->q($hash));
        $db->setQuery($query);
        $result = $db->loadResult();

        return $result > 0;
    }

    public function getPendingUrl(): ?CrawlUrl
    {
        if ($this->hasPendingUrls()) {
            $db     = Factory::getDbo();
            $query  = $db->getQuery(true);
            $query->select('link');
            $query->from($db->qn('#__route66_crawler_queue'));
            $query->where($db->qn('state').' = 0');
            $db->setQuery($query, 0, 1);
            $result = $db->loadResult();

            return unserialize($result);
        }

        return null;
    }
}
