<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Route66\Content\Analyzer;

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Analyzer\Resource;
use Joomla\CMS\Factory;
use Joomla\Component\Content\Site\Helper\RouteHelper;

class Category extends Resource
{
    public function isEditing(): bool
    {
        $application = Factory::getApplication();
        $input       = $application->input;

        return $application->isClient('administrator') && $input->getCmd('option') === 'com_categories' && $input->getCmd('view') === 'category' && $input->getCmd('extension') === 'com_content' && $input->getCmd('layout') === 'edit';
    }

    public function isSaving(): bool
    {
        $application = Factory::getApplication();
        $input       = $application->input;
        $saveTasks   = ['category.save', 'category.apply', 'category.save2copy'];

        return $application->isClient('administrator') && $input->getCmd('option') === 'com_categories' && $input->getCmd('extension') === 'com_content' && \in_array($input->getCmd('task'), $saveTasks);
    }

    public function getOptions(): array
    {
        return [
           'fields' => [
               'title'  => '#jform_title',
               'slug'   => '#jform_alias',
               'text'   => '#jform_description',
               'images' => [
                   '#jform_params_image' => '#jform_params_image_alt',
               ],
               'language' => '#jform_language',
               'metadata' => [
                   'description' => '#jform_metadesc',
               ],
           ],
           'route' => [
                'option'   => 'com_content',
                'view'     => 'category',
                'id'       => '#jform_id',
                'alias'    => '#jform_alias',
                'language' => '#jform_language',
           ],
        ];
    }

    public function getRoute(array $vars): string
    {
        if ($vars['option'] !== 'com_content') {
            return '';
        }

        if ($vars['view'] !== 'category') {
            return '';
        }

        if (!$vars['id']) {
            return '';
        }

        return RouteHelper::getCategoryRoute($vars['id']. ':'. $vars['alias'], $vars['language']);
    }

    public function getResourceKey(string $option, string $view): string
    {
        if ($option !== 'com_content') {
            return '';
        }

        if ($view !== 'category') {
            return '';
        }

        return 'id';
    }

    public function getResourceId(): string
    {
        $id = Factory::getApplication()->input->getInt('id');

        if (!$id) {
            return '';
        }

        return 'com_content.category.'.$id;
    }
}
