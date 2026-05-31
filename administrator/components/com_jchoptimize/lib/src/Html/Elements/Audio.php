<?php

namespace JchOptimize\Core\Html\Elements;

use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

/**
 * @method Audio autoplay(string $value)
 * @method Audio controls(string $value)
 * @method Audio crossorigin(?string $value=null)
 * @method Audio loop(string $value)
 * @method Audio muted(string $value)
 * @method Audio preload(string $value)
 * @method Audio src(string|UriInterface $value)
 * @method string|bool getAutoplay()
 * @method string|bool getControls()
 * @method string|bool getCrossorigin()
 * @method string|bool getLoop()
 * @method string|bool getMuted()
 * @method string|bool getPreload()
 * @method UriInterface|bool getSrc()
 */
final class Audio extends \JchOptimize\Core\Html\Elements\BaseElement
{
    protected string $name = 'audio';
}
