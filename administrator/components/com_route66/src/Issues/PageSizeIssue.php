<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Issues;

use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class PageSizeIssue extends AbstractIssue
{
    public const MAX_RECOMMENDED_SIZE_KB = 450; // 450KB for pure HTML

    protected string $label       = 'COM_ROUTE66_LABEL_PAGE_SIZE';
    protected string $description = 'COM_ROUTE66_ISSUE_PAGE_SIZE';
    protected string $type        = 'danger';

    public function isDetected(): bool
    {
        $size = $this->page->size / 1024;

        return $size > self::MAX_RECOMMENDED_SIZE_KB;
    }

    public function getDescription(): string
    {
        if ($this->page->http_status !== 200) {
            return false;
        }

        $size = round($this->page->size / 1024, 2);

        return Text::sprintf($this->description, $size, self::MAX_RECOMMENDED_SIZE_KB);
    }
}
