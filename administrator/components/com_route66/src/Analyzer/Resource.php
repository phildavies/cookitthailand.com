<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Analyzer;

\defined('_JEXEC') or die;

abstract class Resource
{
    abstract public function isEditing(): bool;
    abstract public function isSaving(): bool;
    abstract public function getOptions(): array;
    abstract public function getRoute(array $vars): string;
    abstract public function getResourceKey(string $option, string $view): string;
    abstract public function getResourceId(): string;
}
