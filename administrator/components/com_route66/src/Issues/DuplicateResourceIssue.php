<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Issues;

use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class DuplicateResourceIssue extends AbstractIssue
{
    protected string $label = 'COM_ROUTE66_LABEL_DUPLICATE_RESOURCE';
    protected string $type  = 'warning';

    public function getDescription(): string
    {
        $link = Route::_('index.php?option=com_route66&view=pages&layout=duplicates&tmpl=component&filter[exclude_id]='.$this->page->id.'&filter[resource_id]='.$this->page->resource_id);

        return Text::sprintf('COM_ROUTE66_ISSUE_DUPLICATE_RESOURCE', $link);
    }

    public function isDetected(): bool
    {
        if ($this->page->http_status !== 200) {
            return false;
        }

        return $this->page->duplicate_resource ? true : false;
    }
}
