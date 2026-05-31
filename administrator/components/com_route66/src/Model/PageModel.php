<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Model;

use Firecoders\Component\Route66\Administrator\Helper\TitleHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Uri\Uri;
use Joomla\Utilities\ArrayHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class PageModel extends AdminModel
{
    protected $text_prefix = 'COM_ROUTE66_PAGE';

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_route66.page', 'page', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        return $form;
    }

    protected function loadFormData()
    {
        $app  = Factory::getApplication();
        $data = $app->getUserState('com_route66.edit.page.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_route66.page', $data);

        return $data;
    }

    protected function preprocessData($context, &$data, $group = 'content')
    {
        if (\is_object($data)) {
            $data->title = TitleHelper::removeSiteName($data->title, $data->language);
        }

        parent::preprocessData($context, $data, $group);
    }

    public function getItem($pk = null)
    {
        $pk = (!empty($pk)) ? $pk : (int) $this->getState($this->getName() . '.id');

        if ($pk) {
            $item = parent::getItem($pk);
        } else {

            $table = $this->getTable();

            $linkHash     = $this->getState('filter.link_hash');
            $resourceId   = $this->getState('filter.resource_id');

            $conditions = [];

            if ($linkHash) {
                $conditions['link_hash'] = $linkHash;
            }

            if ($resourceId) {
                $conditions['resource_id'] = $resourceId;
            }

            if (\count($conditions)) {

                $result = $table->load($conditions);

                if ($result === false) {
                    $this->setError($table->getError() ? $table->getError() : Text::_('JLIB_APPLICATION_ERROR_NOT_EXIST'));
                    return false;
                }
            }

            $properties = get_object_vars($table);
            $item       = ArrayHelper::toObject($properties);
        }

        if ($item) {

            $item->url = Uri::getInstance(Uri::root(false))->toString(['scheme', 'host']) . $item->link;

            $metadataModel  = Factory::getApplication()->bootComponent('com_route66')->getMVCFactory()->createModel('Metadata', 'Administrator', ['ignore_request' => true]);
            $metadataModel->setState('filter.resource_id', $item->resource_id);
            $metadataModel->setState('filter.link_hash', $item->link_hash);
            $item->metadata = $metadataModel->getItem();

            $contentAnalysisModel  = Factory::getApplication()->bootComponent('com_route66')->getMVCFactory()->createModel('ContentAnalysis', 'Administrator', ['ignore_request' => true]);
            $contentAnalysisModel->setState('filter.resource_id', $item->resource_id);
            $contentAnalysisModel->setState('filter.link_hash', $item->link_hash);
            $item->analysis = $contentAnalysisModel->getItem();
        }

        return $item;
    }
}
