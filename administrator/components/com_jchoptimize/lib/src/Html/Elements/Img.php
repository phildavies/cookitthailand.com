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
 * @method Img alt(string $value)
 * @method Img crossorigin(?string $value=null)
 * @method Img decoding(string $value)
 * @method Img elementtiming(string $value)
 * @method Img fetchpriority(string $value)
 * @method Img ismap(string $value)
 * @method Img loading(string $value)
 * @method Img referrerpolicy(string $value)
 * @method Img sizes(string $value)
 * @method Img src(string|UriInterface $value)
 * @method Img srcset(string $value)
 * @method Img width(string $value)
 * @method Img usemap(string $value)
 * @method Img height(string $value)
 * @method string|bool getAlt()
 * @method string|bool getCrossorigin()
 * @method string|bool getDecoding()
 * @method string|bool getElementtiming()
 * @method string|bool getFetchpriority()
 * @method string|bool getIsmap()
 * @method string|bool getLoading()
 * @method string|bool getReferrerpolicy()
 * @method string|bool getSizes()
 * @method UriInterface|bool getSrc()
 * @method string|bool getSrcset()
 * @method string|bool getWidth()
 * @method string|bool getUsemap()
 * @method string|bool getHeight()
 */
final class Img extends \JchOptimize\Core\Html\Elements\BaseElement
{
    protected string $name = 'img';
}
