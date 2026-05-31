<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Model;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Versioning\VersionableModelTrait;
use Joomla\Filter\OutputFilter;
use Joomla\String\StringHelper;
use Joomla\Utilities\ArrayHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class AIToolModel extends AdminModel
{
    use VersionableModelTrait;

    public $typeAlias      = 'com_route66.aitool';
    protected $text_prefix = 'COM_ROUTE66_AI_TOOL';

    protected function canDelete($record)
    {
        if ($record->core) {
            return false;
        }

        return $this->getCurrentUser()->authorise('core.manage', 'com_route66');
    }

    protected function canEditState($record)
    {
        return $this->getCurrentUser()->authorise('core.manage', 'com_route66');
    }

    public function getForm($data = [], $loadData = true)
    {
        $form = $this->loadForm('com_route66.aitool', 'aitool', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }

        if (!Multilanguage::isEnabled()) {
            $form->setFieldAttribute('language', 'type', 'hidden');
            $form->setFieldAttribute('language', 'default', '*');
        }

        return $form;
    }

    protected function loadFormData()
    {
        $app  = Factory::getApplication();
        $data = $app->getUserState('com_route66.edit.aitool.data', []);

        if (empty($data)) {
            $data = $this->getItem();
        }

        $this->preprocessData('com_route66.aitool', $data);

        return $data;
    }

    protected function preprocessForm(Form $form, $data, $group = 'content')
    {
        $item = (object) $data;
        if (isset($item->id) && $item->id && $item->core) {
            $form->setFieldAttribute('created_by', 'type', 'hidden');
            $form->setFieldAttribute('created', 'class', 'hidden');
            $form->setFieldAttribute('created', 'hiddenLabel', 'true');

        }

        parent::preprocessForm($form, $data, $group);
    }

    public function getItem($pk = null)
    {
        $alias = $this->getState('filter.alias');
        $state = $this->getState('filter.state');

        if ($alias || is_numeric($state)) {

            $table = $this->getTable();

            $conditions = [];

            if ($alias) {
                $conditions['alias'] = $alias;
            }

            if (is_numeric($state)) {
                $conditions['state'] = $state;
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

        } else {
            $item = parent::getItem($pk);
        }

        return $item;
    }

    public function save($data)
    {
        if (!$data['alias']) {
            $data['alias'] = 'custom-'. OutputFilter::stringURLSafe($data['title']);
        }

        if (!isset($data['id']) || (int) $data['id'] == 0) {
            [$data['title'], $data['alias']] = $this->generateNewTitle(0, $data['alias'], $data['title']);
        }

        return parent::save($data);
    }

    protected function generateNewTitle($categoryId, $alias, $title)
    {
        $table      = $this->getTable();
        $aliasField = $table->getColumnAlias('alias');
        $titleField = $table->getColumnAlias('title');

        while ($table->load([$aliasField => $alias])) {
            if ($title === $table->$titleField) {
                $title = StringHelper::increment($title);
            }

            $alias = StringHelper::increment($alias, 'dash');
        }

        return [$title, $alias];
    }
}
