<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\Registry\Registry;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects


class SitemapModel extends AdminModel
{
    protected $text_prefix = 'COM_ROUTE66_SITEMAP';

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_route66.sitemap', 'sitemap', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        PluginHelper::importPlugin('route66');
        Factory::getApplication()->triggerEvent('onRoute66SitemapForm', [&$form]);

        $data = $this->loadFormData();
        $form->bind($data);

        return $form;
    }

    protected function loadFormData()
    {
        $app  = Factory::getApplication();
        $data = $app->getUserState('com_route66.edit.sitemap.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_route66.sitemap', $data);

        return $data;
    }

    public function getItem($pk = null)
    {
        $item = parent::getItem($pk);

        if ($item) {
            if (\is_null($item->sources)) {
                $item->sources = '';
            }

            if (\is_null($item->settings)) {
                $item->settings = '';
            }

            $registry      = new Registry();
            $item->sources = $registry->loadString($item->sources);

            $registry       = new Registry();
            $item->settings = $registry->loadString($item->settings);
        }

        return $item;
    }
}
