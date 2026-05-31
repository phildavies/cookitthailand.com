<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/core
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2023 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Core\Spatie\Crawlers;

use _JchOptimizeVendor\GuzzleHttp\Exception\RequestException;
use _JchOptimizeVendor\GuzzleHttp\Psr7\Uri;
use JchOptimize\Core\Admin\API\MessageEventInterface;
use JchOptimize\Core\Helper;
use _JchOptimizeVendor\Psr\Http\Message\ResponseInterface;
use _JchOptimizeVendor\Psr\Http\Message\UriInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerAwareTrait;
use _JchOptimizeVendor\Spatie\Crawler\CrawlObservers\CrawlObserver;

class HtmlCollector extends CrawlObserver implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    /**
     * @var list<array{url:string, html:string}>
     */
    private array $htmls = [];
    private int $numUrls = 0;
    private bool $eventLogging = \false;
    private ?MessageEventInterface $messageEventObj = null;
    /**
     * @return void
     */
    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null)
    {
        $body = $response->getBody();
        $body->rewind();
        $html = $body->getContents();
        if (Helper::validateHtml($html)) {
            $this->htmls[] = ['url' => (string) $url, 'html' => $html];
        }
        if ($this->eventLogging) {
            $originalUrl = Uri::withoutQueryValue($url, 'jchnooptimize');
            $message = 'Crawled URL: ' . $originalUrl;
            $this->logger->info($message);
            $this->messageEventObj?->send($message);
            $this->numUrls++;
        }
    }
    /**
     * @return void
     */
    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null)
    {
        if ($this->eventLogging) {
            $message = 'Failed crawling url: ' . Uri::withoutQueryValue($url, 'jchnooptimize') . ' with message ' . $requestException->getMessage();
            $this->logger->error($message);
            $this->messageEventObj?->send($message);
        }
    }
    /**
     * @return void
     */
    public function finishedCrawling()
    {
        if ($this->eventLogging) {
            $this->messageEventObj?->send('Finished crawling ' . $this->numUrls . ' URLs');
        }
    }
    /**
     * @return list<array{url:string, html:string}>
     */
    public function getHtmls(): array
    {
        return $this->htmls;
    }
    public function setEventLogging(bool $eventLogging): void
    {
        $this->eventLogging = $eventLogging;
    }
    public function setMessageEventObj(?MessageEventInterface $messageEventObj): void
    {
        $this->messageEventObj = $messageEventObj;
    }
}
