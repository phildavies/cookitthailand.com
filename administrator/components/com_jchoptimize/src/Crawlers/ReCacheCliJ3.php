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

namespace JchOptimize\Crawlers;

use JchOptimize\Core\Exception\RequestException;
use JchOptimize\Core\Spatie\CrawlObserver;
use JchOptimize\Core\Uri\ResponseInterface;
use JchOptimize\Psr\Uri\UriInterface;
use Joomla\CMS\Application\CliApplication;

use function defined;

defined('_JEXEC') or die('Restricted Access');

/**
 * @psalm-suppress all
 */
class ReCacheCliJ3 extends CrawlObserver
{
    private CliApplication $cliApp;

    private int $numCrawled = 0;

    public function __construct(CliApplication $cliApp)
    {
        $this->cliApp = $cliApp;
    }

    /**
     * @return void
     */
    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null)
    {
        $this->cliApp->out('Url crawled: ' . $url);
        $this->numCrawled++;
    }

    /**
     * @return void
     */
    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null)
    {
        $this->cliApp->out('Failed crawling url: ' . $url);
    }

    public function getNumCrawled(): int
    {
        return $this->numCrawled;
    }
}
