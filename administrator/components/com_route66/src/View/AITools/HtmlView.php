<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\View\AITools;

use Firecoders\Component\Route66\Administrator\Helper\Route66Helper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\GenericDataException;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class HtmlView extends BaseHtmlView
{
    public $filterForm;
    public $activeFilters = [];
    protected $items      = [];
    protected $pagination;
    protected $state;

    public function display($tpl = null): void
    {
        if (!Route66Helper::isPro()) {
            ToolbarHelper::title(Text::_('COM_ROUTE66_MANAGER_AI_TOOLS'), 'wand-magic-sparkles fa-wand-magic-sparkles');
            $toolbar = Toolbar::getInstance();
            $toolbar->customButton('upgrade')->html('<joomla-toolbar-button><a class="btn btn-success" href="https://www.firecoders.com/joomla-extensions/route-66" target="_blank"> '.Text::_('COM_ROUTE66_UPGRADE_NOW').'</a></joomla-toolbar-button>');
            $this->setLayout('upgrade');
            parent::display($tpl);
            return;
        }

        $model               = $this->getModel();
        $this->items         = $model->getItems();
        $this->pagination    = $model->getPagination();
        $this->state         = $model->getState();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();

        if (\count($errors = $model->getErrors())) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->filterForm->addControlField('task', '');
        $this->filterForm->addControlField('boxchecked', '0');

        $this->addToolbar();

        $params = ComponentHelper::getParams('com_route66');

        if (!$params->get('ai_service') || ($params->get('ai_service') === 'openai' && !$params->get('openai_api_key')) || ($params->get('ai_service') === 'anthropic' && !$params->get('anthropic_api_key'))) {
            Factory::getApplication()->enqueueMessage(Text::_('COM_ROUTE66_AI_TOOLS_NOT_ENABLED_OR_CONFIGURED'), 'warning');
        }

        parent::display($tpl);
    }


    protected function addToolbar(): void
    {
        $user = $this->getCurrentUser();

        $toolbar = Toolbar::getInstance();

        ToolbarHelper::title(Text::_('COM_ROUTE66_MANAGER_AI_TOOLS'), 'wand-magic-sparkles fa-wand-magic-sparkles');

        if ($user->authorise('core.manage', 'com_route66')) {

            $toolbar->addNew('aitool.add');

            $dropdown = $toolbar->dropdownButton('status-group', 'JTOOLBAR_CHANGE_STATUS')
             ->toggleSplit(false)
             ->icon('icon-ellipsis-h')
             ->buttonClass('btn btn-action')
             ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            if ($this->state->get('filter.state') !== '1') {
                $childBar->publish('aitools.publish', 'JTOOLBAR_ENABLE')->listCheck(true);
            }

            if ($this->state->get('filter.state') !== '0') {
                $childBar->unpublish('aitools.unpublish', 'JTOOLBAR_DISABLE')->listCheck(true);
            }

            $childBar->delete('aitools.delete', 'JTOOLBAR_DELETE')
                ->message('JGLOBAL_CONFIRM_DELETE')
                ->listCheck(true);

        }

        if ($user->authorise('core.admin', 'com_route66') || $user->authorise('core.options', 'com_route66')) {
            $toolbar->preferences('com_route66');
        }

    }
}
