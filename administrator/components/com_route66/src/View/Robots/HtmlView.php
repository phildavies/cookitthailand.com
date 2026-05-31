<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\View\Robots;

use Firecoders\Component\Route66\Administrator\Helper\Route66Helper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\FileLayout;
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

    public function display($tpl = null): void
    {
        $model = $this->getModel();

        $this->form  = $model->getForm();
        $this->item  = $model->getItem();

        // Check for errors.
        if (\count($errors = $model->getErrors())) {
            throw new GenericDataException(implode("\n", $errors), 500);
        }

        $this->form->addControlField('task', '');

        $this->addToolbar();
        $this->setLayout('edit');

        parent::display($tpl);
    }


    protected function addToolbar(): void
    {
        Factory::getApplication()->getInput()->set('hidemainmenu', true);
        ToolbarHelper::title(Text::_('COM_ROUTE66_MANAGER_ROBOTS_EDIT'), 'robot fa-robot');

        $toolbar = Toolbar::getInstance();
        $toolbar->apply('robots.apply');
        $toolbar->cancel('robots.cancel');
        if ($this->item->id) {
            if (Route66Helper::isPro()) {
                $params = ComponentHelper::getParams('com_route66');
                if (ComponentHelper::isEnabled('com_contenthistory') && $params->get('save_history', 1)) {
                    $toolbar->versions('com_route66.robots', $this->item->id);
                }
            } else {
                $this->form->setFieldAttribute('version_note', 'type', 'hidden');
                $layout = new FileLayout('toolbar.upgrade', null, ['component' => 'com_route66']);
                $toolbar->appendButton('Custom', $layout->render(['title' => 'JTOOLBAR_VERSIONS', 'icon' => 'icon-code-branch']));
            }
        }

        $toolbar->inlinehelp();
    }
}
