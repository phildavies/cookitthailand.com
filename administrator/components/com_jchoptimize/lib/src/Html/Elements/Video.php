<?php

namespace JchOptimize\Core\Html\Elements;

use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

/**
 * @method Video autoplay(string $value)
 * @method Video controls(string $value)
 * @method Video crossorigin(?string $value=null)
 * @method Video height(string $value)
 * @method Video loop(string $value)
 * @method Video muted(string $value)
 * @method Video playsinline(string $value)
 * @method Video poster(string|UriInterface $value)
 * @method Video preload(string $value)
 * @method Video src(string $value)
 * @method Video width(string $value)
 * @method string|bool getAutoplay()
 * @method string|bool getControls()
 * @method string|bool getCrossorigin()
 * @method string|bool getHeight()
 * @method string|bool getLoop()
 * @method string|bool getMuted()
 * @method string|bool getPlaysinline()
 * @method UriInterface|bool getPoster()
 * @method string|bool getPreload()
 * @method string|bool getSrc()
 * @method string|bool getWidth()
 */
final class Video extends \JchOptimize\Core\Html\Elements\BaseElement
{
    protected string $name = 'video';
}
