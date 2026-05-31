<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Button;

use Joomla\CMS\Button\ActionButton;
use Joomla\CMS\Language\Text;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class PublishedButton extends ActionButton
{
    protected function preprocess()
    {
        $this->addState(1, 'unpublish', 'publish', Text::_('JLIB_HTML_UNPUBLISH_ITEM'), ['tip_title' => Text::_('JPUBLISHED')]);
        $this->addState(0, 'publish', 'unpublish', Text::_('JLIB_HTML_PUBLISH_ITEM'), ['tip_title' => Text::_('JUNPUBLISHED')]);
        $this->addState(-2, 'publish', 'trash', Text::_('JLIB_HTML_PUBLISH_ITEM'), ['tip_title' => Text::_('JTRASHED')]);
    }

    public function render(?int $value = null, ?int $row = null, array $options = [], $publishUp = null, $publishDown = null): string
    {
        return parent::render($value, $row, $options);
    }
}
