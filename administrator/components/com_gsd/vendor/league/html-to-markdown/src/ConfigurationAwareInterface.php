<?php

declare (strict_types=1);
namespace Tassos\Vendor\League\HTMLToMarkdown;

interface ConfigurationAwareInterface
{
    public function setConfig(Configuration $config): void;
}
