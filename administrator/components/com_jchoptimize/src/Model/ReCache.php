<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/core
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2022 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Model;

use JchOptimize\Core\Mvc\Model;
use JchOptimize\Core\Psr\Log\LoggerAwareInterface;
use JchOptimize\Core\Psr\Log\LoggerAwareTrait;
use JchOptimize\Core\Psr\Log\LoggerInterface;
use JchOptimize\Core\Registry;
use JchOptimize\Core\Spatie\Crawler;
use JchOptimize\Core\Spatie\CrawlQueues\CacheCrawlQueue;
use JchOptimize\Core\SystemUri;
use JchOptimize\Crawlers\ReCacheWithRedirect as ReCacheCrawler;

use function defined;

defined('_JEXEC') or die('Restricted Access');

class ReCache extends Model implements LoggerAwareInterface
{
    use LoggerAwareTrait;

    private CacheCrawlQueue $crawlQueue;

    public function __construct(Registry $params, CacheCrawlQueue $crawlQueue, LoggerInterface $log)
    {
        $this->state = $params;
        $this->crawlQueue = $crawlQueue;
        $this->setLogger($log);
    }

    /**
     *
     * @param string $redirectUrl
     * @return void
     */
    public function reCache(string $redirectUrl = ''): void
    {
        $baseUrl = SystemUri::currentBaseFull();
        $crawlLimit = (int)$this->state->get('recache_crawl_limit', 500);
        $concurrency = (int)$this->state->get('recache_concurrency', 20);
        $maxDepth = (int)$this->state->get('recache_max_depth', 5);

        Crawler::create($baseUrl)
            ->setCrawlQueue($this->crawlQueue)
            ->setCrawlObserver(new ReCacheCrawler($this->logger, $redirectUrl))
            ->setTotalCrawlLimit($crawlLimit)
            ->setConcurrency($concurrency)
            ->setMaximumDepth($maxDepth)
            ->startCrawling($baseUrl);
    }
}
