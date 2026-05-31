<?php

declare (strict_types=1);
namespace Tassos\Vendor\League\HTMLToMarkdown\Converter;

use Tassos\Vendor\League\HTMLToMarkdown\ElementInterface;
interface ConverterInterface
{
    public function convert(ElementInterface $element): string;
    /**
     * @return string[]
     */
    public function getSupportedTags(): array;
}
