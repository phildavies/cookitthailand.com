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

namespace JchOptimize\Core\Admin\API;

use _JchOptimizeVendor\Composer\CaBundle\CaBundle;
use JchOptimize\Core\SystemUri;
use Joomla\Input\Input;
use _JchOptimizeVendor\Paragi\PhpWebsocket\Client;
use _JchOptimizeVendor\Paragi\PhpWebsocket\ConnectionException;

use function json_decode;
use function microtime;
use function stream_context_create;

class WebSocket implements \JchOptimize\Core\Admin\API\MessageEventInterface
{
    private Client $connection;
    private string $address = 'socket.jch-optimize.net';
    private int $port = 443;
    private int $timeout = 30;
    public function initialize(): void
    {
        if ($this->isSSl()) {
            $options = ['ssl' => ['cafile' => CaBundle::getBundledCaBundlePath(), 'disable_compression' => \true, 'verify_peer' => \false, 'allow_self_signed' => \true]];
        } else {
            $options = null;
        }
        $context = stream_context_create($options);
        try {
            $this->connection = new Client($this->address, $this->port, '', $errStr, $this->timeout, $this->isSSl(), \false, SystemUri::currentUri()->getPath(), $context);
            $this->send('connected', 'connected');
        } catch (ConnectionException $e) {
            exit;
        }
    }
    public function receive(Input $input): object|null
    {
        $start = microtime(\true);
        do {
            $data = $this->connection->read();
            if ($data) {
                break;
            }
        } while ($start + $this->timeout > microtime(\true));
        return @json_decode($data, \false);
    }
    public function send(string $data, string $type = ''): void
    {
        $msg = [];
        $msg['data'] = $data;
        if ($type) {
            $msg['type'] = $type;
        }
        $this->connection->write(\json_encode($msg));
    }
    public function disconnect(): void
    {
    }
    private function isSSl(): bool
    {
        return \true;
    }
}
