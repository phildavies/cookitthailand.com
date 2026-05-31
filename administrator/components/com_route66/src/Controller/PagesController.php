<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Controller;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class PagesController extends AdminController
{
    protected $text_prefix = 'COM_ROUTE66_PAGES';

    public function getModel($name = 'Page', $prefix = 'Administrator', $config = ['ignore_request' => true])
    {
        return parent::getModel($name, $prefix, $config);
    }

    public function purge()
    {
        $this->checkToken();

        $model  = $this->getModel('Pages');

        if ($model->purge()) {
            $this->setMessage(Text::_('COM_ROUTE66_PURGE_COMPLETED'), 'success');
        } else {
            $this->setMessage(Text::_('COM_ROUTE66_PURGE_FAILED'), 'error');
        }

        $this->setRedirect(
            Route::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_list
                . $this->getRedirectToListAppend(),
                false
            )
        );
    }

    public function reset()
    {
        $this->checkToken();

        $model = $this->getModel('Pages');
        $pages = $model->purge();

        $model    = $this->getModel('Metadata');
        $metadata = $model->purge();

        $model    = $this->getModel('ContentAnalysis');
        $analysis = $model->purge();

        if ($pages && $metadata && $analysis) {
            $this->setMessage(Text::_('COM_ROUTE66_RESET_COMPLETED'), 'success');
        } elseif (!$pages && !$metadata && !$analysis) {
            $this->setMessage(Text::_('COM_ROUTE66_RESET_FAILED'), 'error');
        } else {
            $this->setMessage(Text::_('COM_ROUTE66_RESET_PARTIALY_COMPLETED'), 'warning');
        }

        $this->setRedirect(
            Route::_(
                'index.php?option=' . $this->option . '&view=' . $this->view_list
                . $this->getRedirectToListAppend(),
                false
            )
        );
    }

}
