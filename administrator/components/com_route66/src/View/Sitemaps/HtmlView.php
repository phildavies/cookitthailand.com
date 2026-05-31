<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\View\Sitemaps;

use Joomla\CMS\Helper\ContentHelper;
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


        parent::display($tpl);
    }


    protected function addToolbar(): void
    {
        $canDo   = ContentHelper::getActions('com_route66');
        $user    = $this->getCurrentUser();
        $toolbar = Toolbar::getInstance();

        ToolbarHelper::title(Text::_('COM_ROUTE66_MANAGER_SITEMAPS'), 'sitemap fa-sitemap');

        if ($canDo->get('core.manage')) {
            $toolbar->addNew('sitemap.add');
        }

        if ($canDo->get('core.manage')) {

            $dropdown = $toolbar->dropdownButton('status-group', 'JTOOLBAR_CHANGE_STATUS')
                ->toggleSplit(false)
                ->icon('icon-ellipsis-h')
                ->buttonClass('btn btn-action')
                ->listCheck(true);

            $childBar = $dropdown->getChildToolbar();

            if ($canDo->get('core.manage')) {

                if ($this->state->get('filter.state') !== '1') {
                    $childBar->publish('sitemaps.publish')->listCheck(true);
                }

                if ($this->state->get('filter.state') !== '0') {
                    $childBar->unpublish('sitemaps.unpublish')->listCheck(true);
                }

                $childBar->delete('sitemaps.delete', 'JTOOLBAR_DELETE')
                    ->message('JGLOBAL_CONFIRM_DELETE')
                    ->listCheck(true);

            }
        }


        if ($user->authorise('core.admin', 'com_route66') || $user->authorise('core.options', 'com_route66')) {
            $toolbar->preferences('com_route66');
        }

    }
}
