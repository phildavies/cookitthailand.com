<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Issues;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class NoCompressionIssue extends AbstractIssue
{
    public const COMPRESSION_ENCODINGS = ['gzip', 'compress' , 'deflate', 'br', 'zstd', 'dcb', 'dcz'];

    protected string $label       = 'COM_ROUTE66_LABEL_NO_COMPRESSION';
    protected string $description = 'COM_ROUTE66_ISSUE_NO_COMPRESSION';
    protected string $type        = 'warning';

    public function isDetected(): bool
    {
        if ($this->page->http_status !== 200) {
            return false;
        }

        return !\in_array($this->page->content_encoding, self::COMPRESSION_ENCODINGS);
    }

}
