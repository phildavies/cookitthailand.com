<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/joomla-platform
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2021 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Service;

use JchOptimize\Core\Container\Container;
use JchOptimize\Core\Registry;
use Joomla\CMS\Component\ComponentHelper;
use JchOptimize\Core\Container\ServiceProviderInterface;

use function defined;

defined('_JEXEC') or die('Restricted access');

class ConfigurationProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $container->alias('params', Registry::class)
            ->share(
                Registry::class,
                function (): Registry {
                    //Get a clone so when we get a new instance of the container we get a different object
                    $params = clone ComponentHelper::getParams('com_jchoptimize');

                    if (!defined('JCH_DEBUG')) {
                        define('JCH_DEBUG', ($params->get('debug', 0)));
                    }

                    return new Registry($params);
                }
            );
    }
}
