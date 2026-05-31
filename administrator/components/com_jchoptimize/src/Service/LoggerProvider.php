<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/wordpress-platform
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2021 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Service;

use JchOptimize\Core\Container\Container;
use JchOptimize\Core\Container\ServiceProviderInterface;
use JchOptimize\Core\Interfaces\MvcLoggerInterface;
use JchOptimize\Log\JoomlaLogger;
use Joomla\CMS\Log\Log;

use function defined;

defined('_JEXEC') or die('Restricted access');

class LoggerProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container->alias(MvcLoggerInterface::class, 'logger')
            ->share('logger', function () {
                return new JoomlaLogger();
            });
    }
}
