<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 *  @package   jchoptimize/core
 *  @author    Samuel Marshall <samuel@jch-optimize.net>
 *  @copyright Copyright (c) 2023 Samuel Marshall / JCH Optimize
 *  @license   GNU/GPLv3, or later. See LICENSE file
 *
 *  If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Core\Admin\API;

use Joomla\Input\Input;

class NullEventMessenger implements \JchOptimize\Core\Admin\API\MessageEventInterface
{
    public function __construct()
    {
    }
    public function initialize(): void
    {
        // TODO: Implement initialize() method.
    }
    public function receive(Input $input): object|null|bool
    {
        return null;
    }
    public function send(string $data, string $type = ''): void
    {
        // TODO: Implement send() method.
    }
    public function disconnect(): void
    {
        // TODO: Implement disconnect() method.
    }
}
