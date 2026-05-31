<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\View\Upgrade;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class HtmlView extends BaseHtmlView
{
    public function display($tpl = null): void
    {
        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        Factory::getApplication()->getInput()->set('hidemainmenu', true);
        ToolbarHelper::title(Text::_('COM_ROUTE66_UPGRADE'), 'cog');
        ToolbarHelper::link('index.php?option=com_route66', 'JTOOLBAR_BACK', $this->getLanguage()->isRtl() ? 'arrow-right' : 'arrow-left');
    }
}
