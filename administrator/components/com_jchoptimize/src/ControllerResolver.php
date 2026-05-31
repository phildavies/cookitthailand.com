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

namespace JchOptimize;

use InvalidArgumentException;
use JchOptimize\Core\Container\Container;
use Joomla\DI\Exception\KeyNotFoundException;
use Joomla\Input\Input;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;

use function call_user_func;
use function defined;
use function sprintf;

defined('_JEXEC') or die('Restricted access');

class ControllerResolver
{
    /**
     * @alias \Joomla\DI\Container
     */
    private Container $container;
    /**
     * @var Input
     */
    private Input $input;

    public function __construct(Container $container, Input $input)
    {
        $this->container = $container;
        $this->input = $input;
    }

    /**
     * @return void
     */
    public function resolve()
    {
        $controller = $this->getController();

        if ($this->container->has($controller)) {
            try {
                call_user_func([$this->container->get($controller), 'execute']);
            } catch (KeyNotFoundException $e) {
                throw new InvalidArgumentException(sprintf('Controller %s not found', $controller));
            }
        } else {
            throw new InvalidArgumentException(sprintf('Cannot resolve controller: %s', $controller));
        }
    }

    private function getController(): string
    {
        return $this->input->getString('view', 'ControlPanel');
    }
}
