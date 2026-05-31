<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Site\View\Sitemap;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Joomla\CMS\Uri\Uri;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class XmlView extends HtmlView
{
    protected $xsl;

    public function display($tpl = null)
    {
        $this->item = $this->get('Item');

        if (!$this->item->state) {
            throw new \Exception(404, Text::_('JERROR_PAGE_NOT_FOUND'));
        }

        $application = Factory::getApplication();
        $model       = $this->getModel();
        $this->items = $model->getSitemapItems($this->item, $application->input->getCmd('extension'), $application->input->getInt('limitstart', 0));
        $this->xsl   = Uri::root(false). 'media/route66/xsl/sitemaps.xsl';

        if ($this->item->settings->get('type') == 'news') {
            $config         = Factory::getConfig();
            $this->siteName = $config->get('sitename');
            $document       = Factory::getDocument();
            $language       = $document->getLanguage();
            $parts          = explode('-', $language);
            $this->language = $parts[0];
            $this->setLayout('news');
        }

        parent::display($tpl);
    }

}
