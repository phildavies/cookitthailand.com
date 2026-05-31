<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Site\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\ItemModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\Database\ParameterType;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

/**
 * Banner model for the Joomla Banners component.
 *
 * @since  1.5
 */
class SitemapModel extends ItemModel
{
    protected function populateState()
    {
        $application = Factory::getApplication();

        $pk = $application->getInput()->getInt('id');
        $this->setState('sitemap.id', $pk);

        $params = $application->getParams();
        $this->setState('params', $params);
    }

    public function getItem($pk = null)
    {
        $pk = (int) ($pk ?: $this->getState('sitemap.id'));

        if ($this->_item === null) {
            $this->_item = [];
        }

        if (isset($this->_item[$pk])) {
            return $this->_item[$pk];
        }

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__route66_sitemaps', 'sitemap'));
        $query->where([$db->qn('sitemap.id') . ' = :pk',$db->qn('sitemap.state') . ' = 1']);
        $query->bind(':pk', $pk, ParameterType::INTEGER);
        $db->setQuery($query);
        $data = $db->loadObject();

        if (empty($data)) {
            throw new \Exception(Text::_('JERROR_PAGE_NOT_FOUND'), 404);
        }

        if ($data) {
            $registry      = new Registry();
            $data->sources = $registry->loadString($data->sources);

            $registry       = new Registry();
            $data->settings = $registry->loadString($data->settings);
        }

        $this->_item[$pk] = $data;

        return $this->_item[$pk];
    }


    public function getSitemapIndex($sitemap)
    {
        $urls = [];

        $application = Factory::getApplication();
        $params      = ComponentHelper::getParams('com_route66');
        $limit       = (int) $params->get('sitemap_limit', 500);

        PluginHelper::importPlugin('route66');
        $results     = $application->triggerEvent('onRoute66SitemapItemsCount', [$sitemap]);

        foreach ($results as $result) {
            if ($result['count'] > 0) {
                $urls[] = Route::_('index.php?option=com_route66&view=sitemap&id=' . $sitemap->id . '&extension=' . $result['extension'] . '&format=xml', true, 0, true) ;

                if ($result['count'] > $limit) {
                    $pages = (int) ceil($result['count'] / $limit);

                    for ($page = 1; $page < $pages; ++$page) {
                        $urls[] = Route::_('index.php?option=com_route66&view=sitemap&id=' . $sitemap->id . '&extension=' . $result['extension'] . '&limitstart=' . $page * $limit . '&format=xml', true, 0, true);
                    }
                }
            }
        }

        return $urls;
    }

    public function getSitemapItems($sitemap, $extension, $offset = 0)
    {
        $items = [];

        $params = ComponentHelper::getParams('com_route66');
        $limit  = (int) $params->get('sitemap_limit', 500);

        if ($sitemap->sources->get($extension)) {
            PluginHelper::importPlugin('route66');
            $application = Factory::getApplication();
            $results     = $application->triggerEvent('onRoute66SitemapItems', [$sitemap, $extension, $offset, $limit]);

            foreach ($results as $result) {
                $items = array_merge($items, $result);
            }
        }

        return $items;
    }
}
