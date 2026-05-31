<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Crawler;

use Firecoders\Component\Route66\Administrator\Helper\DocumentHelper;
use Firecoders\Component\Route66\Administrator\Helper\PageHelper;
use Firecoders\Component\Route66\Administrator\Helper\UriHelper;
use GuzzleHttp\Exception\RequestException;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Factory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;
use Spatie\Crawler\CrawlObservers\CrawlObserver;
use Spatie\Robots\RobotsTxt;
use Symfony\Component\Console\Style\SymfonyStyle;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class Observer extends CrawlObserver
{
    protected $start = [];
    protected $robotsTxt;
    protected $io;
    protected $pageId;

    public function __construct(?RobotsTxt $robotsTxt = null, ?SymfonyStyle $io = null)
    {
        $this->robotsTxt = $robotsTxt;
        $this->io        = $io;
    }

    public function setPageId($pageId)
    {
        $this->pageId = $pageId;
    }

    public function willCrawl(UriInterface $url, ?string $linkText): void
    {
        $this->start[(string) $url] = microtime(true);
    }

    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null, ?string $linkText = null): void
    {
        $this->handleCrawl($url, $response);
    }


    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null, ?string $linkText = null): void
    {
        if (!$requestException->hasResponse()) {
            $this->printMessage($requestException->getMessage(), 'error');
            return;
        }

        $response = $requestException->getResponse();
        $this->handleCrawl($url, $response);
    }

    public function finishedCrawling(): void
    {
    }


    protected function handleCrawl(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null, ?string $linkText = null)
    {
        // Duration
        $duration = microtime(true) - $this->start[(string) $url];
        unset($this->start[(string) $url]);

        // Detect resource variables
        $query = UriHelper::parse((string) $url);

        // Get page data
        try {
            [$title, $description, $robots, $language, $contentEncoding, $nodes, $size, $canonical] = DocumentHelper::getPageData($response->getBody(), $response->getHeaders());
        } catch (\Throwable $th) {
            $this->printMessage($th->getMessage(), 'error');
            return;
        }

        // Get link
        $link = UriHelper::getLink($url);

        if (!$link) {
            return;
        }

        // HTTP response status
        $httpStatus = $response->getStatusCode();

        // Response time in ms
        $time = (int) ($duration * 1000);

        // Blocked by robots.txt
        $robotsTxtBlocked = $this->robotsTxt && $this->robotsTxt->allows((string) $url) === false ? 1 : 0;

        // Crawl time
        $date    = new Date();
        $crawled = $date->toSql();

        // Resource vars
        $component = $query['option'] ?? null;
        $view      = $query['view'] ?? null;
        $key       = null;

        if ($component && $view) {
            $variable = PageHelper::getResourceKey($component, $view);

            if (isset($query[$variable]) && is_numeric($query[$variable]) && $query[$variable] > 0) {
                $key = $query[$variable];
            }
        }

        // Save data
        $data = [
           'http_status'        => $httpStatus,
           'link'               => $link,
           'component'          => $component,
           'view'               => $view,
           'key'                => $key,
           'title'              => $title,
           'description'        => $description,
           'robots'             => $robots,
           'language'           => $language,
           'size'               => $size,
           'time'               => $time,
           'content_encoding'   => $contentEncoding,
           'dom_nodes'          => $nodes,
           'canonical'          => $canonical,
           'crawled'            => $crawled,
           'robots_txt_blocked' => $robotsTxtBlocked,
        ];

        if ($this->pageId) {
            $data['id'] = $this->pageId;
        }

        $model  = Factory::getApplication()->bootComponent('com_route66')->getMVCFactory()->createModel('Page', 'Administrator', ['ignore_request' => true]);
        $result = $model->save($data);

        if (!$result) {
            $this->printMessage($model->getError(), 'error');
            return;
        }

        $this->printMessage('['.$httpStatus.'] '.(string) $url, 'success');
    }

    protected function printMessage($message, $type = 'info')
    {
        $application = Factory::getApplication();

        if ($application->isClient('cli') && $this->io) {
            $this->io->writeLn($message);

            return;
        }

        if ($application->isClient('administrator')) {
            $application->enqueueMessage($message, $type);
        }
    }

}
