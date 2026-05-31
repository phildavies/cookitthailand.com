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

class ContentAnalysisModel extends AdminModel
{
    public function getForm($data = [], $loadData = true)
    {
        return false;
    }

    public function getItem($pk = null)
    {
        if (is_numeric($pk)) {
            return parent::getItem($pk);
        }

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

        return $item;
    }

    public function purge()
    {
        $db = $this->getDatabase();

        try {
            $db->truncateTable('#__route66_content_analysis');
        } catch (\Exception) {
            return false;
        }

        return true;
    }
}
