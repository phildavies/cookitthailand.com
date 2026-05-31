<?php

namespace JchOptimize\Core\Html\Elements;

use _JchOptimizeVendor\Psr\Http\Message\UriInterface;

/**
 * @method Input alt(string $value)
 * @method Input autocomplete(string $value)
 * @method Input disabled(string $value)
 * @method Input form(string $value)
 * @method Input name(string $value)
 * @method Input readonly(string $value)
 * @method Input required(string $value)
 * @method Input height(string $value)
 * @method Input src(string|UriInterface $value)
 * @method Input type(string $value)
 * @method Input width(string $value)
 * @method string|bool getAlt()
 * @method string|bool getAutocomplete()
 * @method string|bool getDisabled()
 * @method string|bool getForm()
 * @method string|bool getName()
 * @method string|bool getReadonly()
 * @method string|bool getRequired()
 * @method string|bool getHeight()
 * @method UriInterface|bool getSrc()
 * @method string|bool getType()
 * @method string|bool getWidth()
 */
final class Input extends \JchOptimize\Core\Html\Elements\BaseElement
{
    protected string $name = 'input';
}
