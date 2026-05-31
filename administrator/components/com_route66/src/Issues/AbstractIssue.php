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

abstract class AbstractIssue implements IssueInterface
{
    protected object $page;
    protected string $label;
    protected string $description;
    protected string $type;

    public function __construct(object $page)
    {
        $this->page = $page;
    }

    public function getLabel(): string
    {
        return Text::_($this->label);
    }

    public function getDescription(): string
    {
        return Text::_($this->description);
    }

    public function getType(): string
    {
        return $this->type;
    }
}
