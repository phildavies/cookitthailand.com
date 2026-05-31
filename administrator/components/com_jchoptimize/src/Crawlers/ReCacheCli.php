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
use Joomla\CMS\Language\Text;
use Symfony\Component\Console\Style\SymfonyStyle;

use function defined;

defined('_JEXEC') or die('Restricted Access');

class ReCacheCli extends CrawlObserver
{
    private SymfonyStyle $symfonyStyle;

    private int $numCrawled = 0;

    public function __construct(SymfonyStyle $symfonyStyle)
    {
        $this->symfonyStyle = $symfonyStyle;
    }

    /**
     * @return void
     */
    public function crawled(UriInterface $url, ResponseInterface $response, ?UriInterface $foundOnUrl = null)
    {
        $this->symfonyStyle->writeln(Text::sprintf('COM_JCHOPTIMIZE_CLI_URL_CRAWLED', $url));
        $this->numCrawled++;
    }

    /**
     * @return void
     */
    public function crawlFailed(UriInterface $url, RequestException $requestException, ?UriInterface $foundOnUrl = null)
    {
        $this->symfonyStyle->comment(Text::sprintf('COM_JCHOPTIMIZE_CLI_URL_CRAWL_FAILED', $url));
    }

    public function getNumCrawled(): int
    {
        return $this->numCrawled;
    }
}
