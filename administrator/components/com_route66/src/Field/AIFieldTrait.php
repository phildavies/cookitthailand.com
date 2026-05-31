<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2024 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Layout\FileLayout;

trait AIFieldTrait
{
    protected $alias;

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        $this->alias  = (string) $element['alias'];

        return parent::setup($element, $value, $group);
    }

    protected function getInput()
    {
        $params = ComponentHelper::getParams('com_route66');

        if (!$params->get('ai_service')) {
            return parent::getInput();
        }

        if ($params->get('ai_service') === 'openai' && !$params->get('openai_api_key')) {
            return parent::getInput();
        }

        if ($params->get('ai_service') === 'anthropic' && !$params->get('anthropic_api_key')) {
            return parent::getInput();
        }

        if (!$this->alias) {
            return parent::getInput();
        }

        $tool = $this->getTool();

        if (!$tool) {
            return parent::getInput();
        }

        $input = parent::getInput();

        $layout = new FileLayout('form.field.ai.button', null, ['component' => 'com_route66']);
        $button = $layout->render(['tool' => $tool]);

        return $button.$input;
    }

    protected function getTool()
    {
        $model =  Factory::getApplication()->bootComponent('com_route66')->getMVCFactory()->createModel('AITool', 'Administrator', ['ignore_request' => true]);
        $model->setState('filter.alias', $this->alias);
        $model->setState('filter.state', 1);
        $tool = $model->getItem();

        return $tool;
    }
}
