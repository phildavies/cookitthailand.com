<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Site\Controller;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class DisplayController extends \Joomla\CMS\MVC\Controller\BaseController
{
    public function display($cachable = false, $urlparams = false)
    {
        $cachable = true;

        $user = $this->app->getIdentity();

        if ($user->get('id') || $this->input->getMethod() === 'POST') {
            $cachable = false;
        }

        $safeurlparams = [
            'extension'  => 'CMD',
            'id'         => 'UINT',
            'limit'      => 'UINT',
            'limitstart' => 'UINT',
        ];

        if ($this->app->input->get('view') === 'sitemapindex') {
            $this->app->input->set('view', 'SitemapIndex');
        }

        parent::display($cachable, $safeurlparams);

        return $this;
    }
}
