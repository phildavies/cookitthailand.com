<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Route66\Menus\Model;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Registry\Registry;

class Sitemap extends ListModel
{
    public function getListQuery()
    {
        $user = Factory::getApplication()->getIdentity();
        $db   = Factory::getDbo();

        $query = $db->getQuery(true);
        $query->select($db->qn('id') . ',' . $db->qn('type') . ',' . $db->qn('params') . ',' . $db->qn('home') . ',' . $db->qn('language'));
        $query->from($db->qn('#__menu'));
        $query->where($db->qn('client_id') . ' = 0');
        $query->where($db->qn('published') . ' = 1');
        $query->where($db->qn('access') . ' IN (' . implode(',', $user->getAuthorisedViewLevels()) . ')');
        $query->where('(' . $db->qn('type') . ' = ' . $db->q('component') . ' OR ' . $db->qn('type') . ' = ' . $db->q('alias') . ')');

        $sources = $this->getState('sources');
        if ($sources && $sources instanceof Registry) {

            $menuTypes = $sources->get('menuTypes');

            if ($sources->get('menus') == 2 && \is_array($menuTypes) && \count($menuTypes)) {

                $conditions = [];

                foreach ($menuTypes as $menuType) {
                    $conditions[] = $db->qn('menutype') . ' = ' . $db->q($menuType);
                }

                $query->where('(' . implode(' OR ', $conditions) . ')');
            }
        }

        $query->order($db->qn('id'));

        return $query;
    }

    public function getItems()
    {
        $items = parent::getItems();

        $rows = [];

        foreach ($items as $item) {

            if ($item->type == 'alias') {
                $params = new Registry($item->params);
                $item   = $this->getMenuItem((int) $params->get('aliasoptions'));
            }

            if ($item->home) {
                $url = rtrim(Uri::root(false), '/');
            } else {
                $route = 'index.php?Itemid=' . $item->id;
                if (Multilanguage::isEnabled() && $item->language != '*') {
                    $route .= '&lang=' . $item->language;
                }
                $url = Route::link('site', $route, false, Route::TLS_IGNORE, true);
            }

            $rows[$url] = (object) ['url' => $url, 'images' => [], 'videos' => [], 'modifiedDate' => null, 'publicationDate' => null];
        }

        $items = array_values($rows);

        return $items;
    }

    protected function getMenuItem(int $id)
    {
        $db     = $this->getDatabase();
        $query  = $db->getQuery(true);
        $query->select($db->qn('id'));
        $query->select($db->qn('home'));
        $query->select($db->qn('language'));
        $query->from($db->qn('#__menu'));
        $query->where($db->qn('id') . ' = ' . $id);
        $db->setQuery($query, 0, 1);
        $item = $db->loadObject();

        return $item;
    }
}
