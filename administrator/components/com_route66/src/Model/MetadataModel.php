<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Model;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Utilities\ArrayHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class MetadataModel extends AdminModel
{
    public function getForm($data = [], $loadData = true)
    {
        return false;
    }

    public function getItem($pk = null)
    {
        if (is_numeric($pk)) {
            $item = parent::getItem($pk);
        } else {
            $result = false;

            $table = $this->getTable();

            if ($pageId = $this->getState('filter.page_id')) {
                $result = $table->load(['page_id' => $pageId]);
            }

            if (!$result && $resourceId = $this->getState('filter.resource_id')) {
                $result = $table->load(['resource_id' => $resourceId]);
            }

            if (!$result && $linkHash = $this->getState('filter.link_hash')) {
                $result = $table->load(['link_hash' => $linkHash]);
            }

            if ($result === false) {
                $this->setError($table->getError() ? $table->getError() : Text::_('JLIB_APPLICATION_ERROR_NOT_EXIST'));
                return false;
            }

            $properties = get_object_vars($table);
            $item       = ArrayHelper::toObject($properties);
        }

        if ($item && ($item->x_title || $item->x_description || $item->x_image)) {
            $item->customize_x = 1;
        }

        return $item;
    }

    public function save($data)
    {
        if (!$data['customize_x']) {
            $data['x_title']       = null;
            $data['x_description'] = null;
            $data['x_image']       = null;
        }

        return parent::save($data);
    }

    public function purge()
    {
        $db = $this->getDatabase();

        try {
            $db->truncateTable('#__route66_metadata');
        } catch (\Exception) {
            return false;
        }

        return true;
    }

}
