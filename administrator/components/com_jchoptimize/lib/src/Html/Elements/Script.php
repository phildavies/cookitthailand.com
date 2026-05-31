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

namespace JchOptimize\Core\Html\Elements;

use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

/**
 * @method Script async()
 * @method Script crossorigin(?string $value=null)
 * @method Script defer()
 * @method Script fetchpriority(string $value)
 * @method Script integrity(string $value)
 * @method Script nomodule(string $value)
 * @method Script nonce(string $value)
 * @method Script referrerpolicy(string $value)
 * @method Script src(string|UriInterface $value)
 * @method Script type(string $value)
 * @method bool getAsync()
 * @method string|bool getCrossorigin()
 * @method bool getDefer()
 * @method string|bool getFetchpriority()
 * @method string|bool getIntegrity()
 * @method string|bool getNomodule()
 * @method string|bool getNonce()
 * @method string|bool getReferrerpolicy()
 * @method UriInterface|null getSrc()
 * @method string|bool getType()
 */
final class Script extends \JchOptimize\Core\Html\Elements\BaseElement
{
    protected string $name = 'script';
}
