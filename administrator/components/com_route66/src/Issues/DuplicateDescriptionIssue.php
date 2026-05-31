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

class DuplicateDescriptionIssue extends AbstractIssue
{
    protected string $label = 'COM_ROUTE66_LABEL_DUPLICATE_DESCRIPTION';
    protected string $type  = 'warning';

    public function getDescription(): string
    {
        $link = Route::_('index.php?option=com_route66&view=pages&layout=duplicates&tmpl=component&filter[exclude_id]='.$this->page->id.'&filter[description_hash]='.$this->page->description_hash);

        return Text::sprintf('COM_ROUTE66_ISSUE_DUPLICATE_DESCRIPTION', $link);
    }

    public function isDetected(): bool
    {
        if ($this->page->http_status !== 200) {
            return false;
        }

        return $this->page->duplicate_description ? true : false;
    }

}
