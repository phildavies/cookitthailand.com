<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Site\View\SitemapIndex;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\Exception;
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
        $model       = Factory::getApplication()->bootComponent('com_route66')->getMVCFactory()->createModel('Sitemap', 'Site');
        $item        = $model->getItem();

        if (!$item) {
            throw new Exception\ResourceNotFound(Text::_('JERROR_PAGE_NOT_FOUND'), 404);
        }

        $this->items = $model->getSitemapIndex($item);
        $this->xsl   = Uri::root(false). 'media/route66/xsl/sitemaps.xsl';

        parent::display($tpl);
    }

}
