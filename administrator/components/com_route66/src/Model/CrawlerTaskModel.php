<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Model;

use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Utilities\ArrayHelper;

\defined('_JEXEC') or die;

class CrawlerTaskModel extends AdminModel
{
    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        if ($item) {
            $item->queue = unserialize($item->queue);
        }

        return $item;
    }

    public function getActiveTask()
    {
        $table  = $this->getTable();
        $result = $table->load(['state' => 0]);

        if (!$result) {
            return false;
        }

        $properties = get_object_vars($table);
        $item       = ArrayHelper::toObject($properties);

        return $item;
    }

    public function save($data)
    {
        $data['queue'] = serialize($data['queue']);

        return parent::save($data);
    }

    public function getForm($data = [], $loadData = true)
    {
        return false;
    }

    public function clearQueue()
    {
        $db = $this->getDatabase();

        try {
            $db->truncateTable('#__route66_crawler_queue');
        } catch (\Exception) {
            return false;
        }

        return true;
    }
}
