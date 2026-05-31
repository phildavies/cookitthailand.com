<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Router\Route;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class AIToolsModel extends ListModel
{
    public function __construct($config = [])
    {
        if (empty($config['filter_fields'])) {
            $config['filter_fields'] = [
                'id', 'aitool.id',
                'title', 'aitool.title',
                'state', 'aitool.state',
                'ordering', 'aitool.ordering',
                'target', 'aitool.target',
            ];
        }

        parent::__construct($config);
    }

    protected function populateState($ordering = 'aitool.id', $direction = 'asc')
    {
        $application = Factory::getApplication();

        if ($layout = $application->input->get('layout')) {
            $this->context .= '.' . $layout;
        }

        $this->setState('params', ComponentHelper::getParams('com_route66'));

        $this->getUserStateFromRequest($this->context . '.filter.search', 'filter_search');
        $this->getUserStateFromRequest($this->context . '.filter.state', 'filter_state', '', 'int');
        $this->getUserStateFromRequest($this->context . '.filter.target', 'filter_target', '', 'string');


        parent::populateState($ordering, $direction);
    }

    protected function getStoreId($id = '')
    {
        $id .= ':' . $this->getState('filter.state');
        $id .= ':' . $this->getState('filter.id');
        $id .= ':' . $this->getState('filter.target');
        $id .= ':' . $this->getState('filter.search');

        return parent::getStoreId($id);
    }

    protected function getListQuery()
    {
        $db    = $this->getDatabase();
        $query = $db->getQuery(true);

        $query->select($this->getState('list.select', 'aitool.*'));
        $query->from('#__route66_ai_tools AS aitool');

        $state = $this->getState('filter.state');
        if (is_numeric($state)) {
            $query->where($db->qn('aitool.state') . ' = ' . (int) $state);
        }

        $target = $this->getState('filter.target');
        if ($target) {
            $query->where($db->qn('aitool.target') . ' = ' . $db->q($target));
        }

        $search = $this->getState('filter.search');

        if ($search) {
            if (is_numeric($search)) {
                $query->where($db->qn('aitool.id') . ' = '.$db->q($search));
            } else {
                $search = '%' . trim($search) . '%';
                $query->where($db->qn('aitool.title') . ' LIKE '.$db->q($search));
            }
        }

        $query->order($this->getState('list.ordering', 'aitool.ordering') . ' ' . $this->getState('list.direction', 'ASC'));

        return $query;
    }


    public function getItems()
    {
        $items = parent::getItems();

        foreach ($items as $key => $item) {
            $item->editLink = Route::_('index.php?option=com_route66&task=aitool.edit&id=' . $item->id);
        }

        return $items;
    }
}
