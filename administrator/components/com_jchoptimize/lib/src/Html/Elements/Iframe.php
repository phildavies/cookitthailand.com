<?php

namespace JchOptimize\Core\Html\Elements;

use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

/**
 * @method Iframe allow(string $value)
 * @method Iframe allowfullscreen(string $value)
 * @method Iframe height(string $value)
 * @method Iframe loading(string $value)
 * @method Iframe name(string $value)
 * @method Iframe referrerpolicy(string $value)
 * @method Iframe sandbox(string $value)
 * @method Iframe src(string|UriInterface $value)
 * @method Iframe srcdoc(string $value)
 * @method Iframe width(string $value)
 * @method string|bool getAllow()
 * @method string|bool getAllowfullscreen()
 * @method string|bool getHeight()
 * @method string|bool getLoading()
 * @method string|bool getName()
 * @method string|bool getReferrerpolicy()
 * @method string|bool getSandbox()
 * @method UriInterface|bool getSrc()
 * @method string|bool getSrcdoc()
 * @method string|bool getWidth()
 */
final class Iframe extends \JchOptimize\Core\Html\Elements\BaseElement
{
    protected string $name = 'iframe';
}
