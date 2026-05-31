<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\View\Pages;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
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
    protected $items;
    protected $pagination;
    protected $state;
    public $filterForm;
    public $activeFilters;

    public function display($tpl = null): void
    {
        $model               = $this->getModel();
        $this->items         = $model->getItems();
        $this->pagination    = $model->getPagination();
        $this->state         = $model->getState();
        $this->filterForm    = $model->getFilterForm();
        $this->activeFilters = $model->getActiveFilters();

        $crawlerTaskModel  = Factory::getApplication()->bootComponent('com_route66')->getMVCFactory()->createModel('CrawlerTask', 'Administrator', ['ignore_request' => true]);
        $this->crawlerTask = $crawlerTaskModel->getActiveTask();

        if (\count($errors = $model->getErrors())) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $params = ComponentHelper::getParams('com_route66');
        $limit  = $params->get('total_crawl_limit', 0);
        if ($limit) {
            $session = Factory::getApplication()->getSession();
            if ($limit <= $this->pagination->total && !$session->get('com_route66.crawl_limit_message_shown')) {
                Factory::getApplication()->enqueueMessage(Text::_('COM_ROUTE66_CRAWL_LIMIT_REACHED'));
                $session->set('com_route66.crawl_limit_message_shown', 1);
            }
        }

        $this->filterForm->addControlField('task', '');
        $this->filterForm->addControlField('boxchecked', '0');

        if ($this->filterForm->getValue('issues', 'filter')) {
            $this->filterForm->setValue('response_type', 'filter', 'normal');
            $this->filterForm->setFieldAttribute('response_type', 'disabled', 'disabled', 'filter');
        } elseif ($this->filterForm->getValue('response_type', 'filter') && $this->filterForm->getValue('response_type', 'filter') !== 'normal') {
            $this->filterForm->setValue('issues', 'filter', null);
            $this->filterForm->setFieldAttribute('issues', 'disabled', 'disabled', 'filter');
            $this->filterForm->setValue('seo_rating', 'filter', null);
            $this->filterForm->setFieldAttribute('seo_rating', 'disabled', 'disabled', 'filter');
            $this->filterForm->setValue('readability_rating', 'filter', null);
            $this->filterForm->setFieldAttribute('readability_rating', 'disabled', 'disabled', 'filter');
        }

        $this->addToolbar();

        parent::display($tpl);
    }


    protected function addToolbar(): void
    {
        $canDo   = ContentHelper::getActions('com_route66');
        $user    = $this->getCurrentUser();
        $toolbar = Toolbar::getInstance();

        ToolbarHelper::title(Text::_('COM_ROUTE66_MANAGER_PAGES'), 'link fa-link');

        if ($canDo->get('core.manage')) {
            $toolbar->confirmButton('crawl', 'COM_ROUTE66_CRAWL', 'pages.crawl')->icon('icon-refresh')->message('COM_ROUTE66_CRAWL_START_WARNING');
            $toolbar->confirmButton('delete', 'COM_ROUTE66_PURGE', 'pages.purge')->name('delete')->message('COM_ROUTE66_PURGE_PAGES_WARNING');
            $toolbar->confirmButton('reset', 'COM_ROUTE66_PURGE_ALL', 'pages.reset')->name('delete')->icon('icon-warning')->message('COM_ROUTE66_PURGE_ALL_WARNING');
        }

        if ($user->authorise('core.admin', 'com_route66') || $user->authorise('core.options', 'com_route66')) {
            $toolbar->preferences('com_route66');
        }

    }
}
