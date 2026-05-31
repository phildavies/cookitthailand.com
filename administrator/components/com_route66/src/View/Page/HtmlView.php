<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\View\Page;

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
    protected $form;
    protected $item;
    protected $state;

    public function display($tpl = null): void
    {
        $model = $this->getModel();

        $this->form  = $model->getForm();
        $this->item  = $model->getItem();
        $this->state = $model->getState();

        // Check for errors.
        if (\count($errors = $model->getErrors())) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->form->addControlField('task', '');
        $this->form->addControlField('return', Factory::getApplication()->getInput()->getBase64('return', ''));

        $this->addToolbar();

        parent::display($tpl);
    }


    protected function addToolbar(): void
    {
        Factory::getApplication()->getInput()->set('hidemainmenu', true);
        ToolbarHelper::title(Text::_('COM_ROUTE66_MANAGER_PAGE_EDIT'), 'link fa-link');

        $toolbar = Toolbar::getInstance();
        $toolbar->apply('page.apply');
        $toolbar->save('page.save');
        $toolbar->cancel('page.cancel');
        $toolbar->inlinehelp();
    }
}
