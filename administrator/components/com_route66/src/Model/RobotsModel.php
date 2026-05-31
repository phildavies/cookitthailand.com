<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Versioning\VersionableModelTrait;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class RobotsModel extends AdminModel
{
    use VersionableModelTrait;

    public $typeAlias = 'com_route66.robots';

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_route66.robots', 'robots', ['control' => 'jform', 'load_data' => $loadData]);

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

        return $data;
    }


    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        if ($item) {
            // Always sync from filesystem
            $contents       = file_exists(JPATH_SITE.'/robots.txt') ? file_get_contents(JPATH_SITE.'/robots.txt') : '';
            $item->contents = $contents;
        }

        return $item;
    }
}
