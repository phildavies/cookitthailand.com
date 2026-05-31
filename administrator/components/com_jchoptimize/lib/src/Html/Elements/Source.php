<?php

namespace JchOptimize\Core\Html\Elements;

use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

/**
 * @method Source type(string $value)
 * @method Source src(string|UriInterface $value)
 * @method Source srcset(string $value)
 * @method Source sizes(string $value)
 * @method Source media(string $value)
 * @method Source height(string $value)
 * @method Source width(string $value)
 * @method string|bool getType()
 * @method UriInterface|bool getSrc()
 * @method string|bool getSrcset()
 * @method string|bool getSizes()
 * @method string|bool getMedia()
 * @method string|bool getHeight()
 * @method string|bool getWidth()
 */
final class Source extends \JchOptimize\Core\Html\Elements\BaseElement
{
    protected string $name = 'source';
}
