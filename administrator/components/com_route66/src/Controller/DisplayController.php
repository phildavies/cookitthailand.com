<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Controller;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects


class DisplayController extends BaseController
{
    protected $default_view = 'pages';

    public function display($cachable = false, $urlparams = [])
    {
        $view   = $this->input->get('view');
        $layout = $this->input->get('layout');
        $id     = $this->input->getInt('id');

        if ($view === 'robots') {
            $id = 1;
            $this->input->set('id', $id);
        }

        if ($view === 'sitemap' || $view === 'robots' || ($view === 'aitool' && $layout != 'modal')) {

            if (!$this->checkEditId('com_route66.edit.'.$view, $id)) {
                $this->setMessage(Text::sprintf('JLIB_APPLICATION_ERROR_UNHELD_ID', $id), 'error');
                $this->setRedirect(Route::_('index.php?option=com_route66', false));
                return false;
            }

        }

        return parent::display();
    }
}
