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
 * @method Link as(string $value)
 * @method Link crossorigin(?string $value=null)
 * @method Link fetchpriority(string $value)
 * @method Link href(string|UriInterface $value)
 * @method Link hreflang(string $value)
 * @method Link imagesizes(string $value)
 * @method Link imagesrcset(string $value)
 * @method Link integrity(string $value)
 * @method Link media(string $value)
 * @method Link referrerpolicy(string $value)
 * @method Link rel(string $value)
 * @method Link sizes(string $value)
 * @method Link title(string $value)
 * @method Link type(string $value)
 * @method string|bool getAs()
 * @method string|bool getCrossorigin()
 * @method string|bool getFetchpriority()
 * @method UriInterface|bool getHref()
 * @method string|bool getHreflang()
 * @method string|bool getImagesizes()
 * @method string|bool getImagesrcset()
 * @method string|bool getIntegrity()
 * @method string|bool getMedia()
 * @method string|bool getReferrerpolicy()
 * @method string|bool getRel()
 * @method string|bool getSizes()
 * @method string|bool getTitle()
 * @method string|bool getType()
 */
final class Link extends \JchOptimize\Core\Html\Elements\BaseElement
{
    protected string $name = 'link';
}
