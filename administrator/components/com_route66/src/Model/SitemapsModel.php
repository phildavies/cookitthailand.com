<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects


class SitemapsModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'sitemap.id',
                'title', 'sitemap.title',
                'state', 'sitemap.state',
            ];
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = 'sitemap.id', $direction = 'desc')
    {
        $this->setState('params', ComponentHelper::getParams('com_route66'));

        $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'int');

        parent::populateState($ordering, $direction);
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.state');
        $id .= ':' . $this->getState('filter.id');
        $id .= ':' . $this->getState('filter.search');

        return parent::getStoreId($id);
    }

    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select($this->getState('list.select', 'sitemap.*'));
        $query->from('#__route66_sitemaps AS sitemap');

        if (is_numeric($this->getState('filter.state'))) {
            $query->where($db->qn('sitemap.state') . ' = ' . (int) $this->getState('filter.state'));
        }

        $search = $this->getState('filter.search');

        if ($search) {
            if (is_numeric($search)) {
                $query->where($db->qn('sitemap.id') . ' = '.$db->q($search));
            } else {
                $search = '%' . trim($search) . '%';
                $query->where($db->qn('sitemap.title') . ' LIKE '.$db->q($search));
            }
        }

        $query->order($this->getState('list.ordering', 'sitemap.id') . ' ' . $this->getState('list.direction', 'DESC'));

        return $query;
    }


    public function getItems()
    {
        $items = parent::getItems();

        foreach ($items as $key => $item) {
            $item->previewLink = Route::link('site', 'index.php?option=com_route66&view=sitemapindex&id=' . $item->id . '&format=xml', true, Route::TLS_IGNORE, true);
            $item->editLink    = Route::_('index.php?option=com_route66&task=sitemap.edit&id=' . $item->id);
        }

        return $items;
    }
}
