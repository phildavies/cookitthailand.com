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

namespace JchOptimize\Log;

use Joomla\CMS\Log\DelegatingPsrLogger;
use Joomla\CMS\Log\Log;
use Psr\Log\AbstractLogger;

use function defined;

defined('_JEXEC') or die('Restricted Access');

class JoomlaLogger extends AbstractLogger
{
    private DelegatingPsrLogger $logger;

    public function __construct()
    {
        Log::addLogger(
            [
                'text_file' => 'com_jchoptimize.logs.php'
            ],
            Log::ALL,
            ['com_jchoptimize']
        );

        $this->logger = Log::createDelegatedLogger();
    }

    public function log($level, $message, array $context = []): void
    {
        $context = array_merge($context, ['category' => 'com_jchoptimize']);

        $this->logger->log($level, (string)$message, $context);
    }
}
