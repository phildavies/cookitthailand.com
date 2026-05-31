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

class ExcessiveDOMSizeIssue extends AbstractIssue
{
    public const MAX_DOM_NODES = 2000; // Google's recommendation is 1500, We use 2000

    protected string $label = 'COM_ROUTE66_LABEL_LARGE_DOM';
    protected string $type  = 'danger';

    public function getDescription(): string
    {
        return Text::sprintf('COM_ROUTE66_ISSUE_LARGE_DOM', $this->page->dom_nodes, self::MAX_DOM_NODES);
    }

    public function isDetected(): bool
    {
        if ($this->page->http_status !== 200) {
            return false;
        }

        return $this->page->dom_nodes > self::MAX_DOM_NODES;
    }
}
