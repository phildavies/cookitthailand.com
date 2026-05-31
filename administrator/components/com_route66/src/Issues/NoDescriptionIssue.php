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

class NoDescriptionIssue extends AbstractIssue
{
    protected string $label       = 'COM_ROUTE66_LABEL_NO_DESCRIPTION';
    protected string $description = 'COM_ROUTE66_ISSUE_NO_DESCRIPTION';
    protected string $type        = 'danger';

    public function isDetected(): bool
    {
        if ($this->page->http_status !== 200) {
            return false;
        }

        return $this->page->description_length < 1;
    }

}
