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

class Article extends Resource
{
    public function isEditing(): bool
    {
        $application = Factory::getApplication();
        $input       = $application->input;

        if ($application->isClient('administrator') && $input->getCmd('option') === 'com_content' && $input->getCmd('view') === 'article' && $input->getCmd('layout') === 'edit') {
            return true;
        }

        if ($application->isClient('site') && $input->getCmd('option') === 'com_content' && $input->getCmd('view') === 'form' && $input->getCmd('layout') === 'edit') {
            return true;
        }

        return false;
    }

    public function isSaving(): bool
    {
        $application = Factory::getApplication();
        $input       = $application->input;
        $saveTasks   = ['article.save', 'article.apply', 'article.save2new', 'article.save2menu', 'article.save2copy'];

        return $application->isClient('administrator') && $input->getCmd('option') === 'com_content' && \in_array($input->getCmd('task'), $saveTasks);
    }

    public function getOptions(): array
    {
        return [
           'fields' => [
               'title'  => '#jform_title',
               'slug'   => '#jform_alias',
               'text'   => '#jform_articletext',
               'images' => [
                   '#jform_images_image_intro'    => '#jform_images_image_intro_alt',
                   '#jform_images_image_fulltext' => '#jform_images_image_fulltext_alt',
               ],
               'language' => '#jform_language',
               'metadata' => [
                   'title'       => '#jform_attribs_article_page_title',
                   'description' => '#jform_metadesc',
               ],
           ],
           'route' => [
                'option'   => 'com_content',
                'view'     => 'article',
                'id'       => '#jform_id',
                'alias'    => '#jform_alias',
                'catid'    => '#jform_catid',
                'language' => '#jform_language',
           ],
        ];
    }

    public function getRoute(array $vars): string
    {
        if ($vars['option'] !== 'com_content') {
            return '';
        }

        if ($vars['view'] !== 'article') {
            return '';
        }

        if (!$vars['id']) {
            return '';
        }

        return RouteHelper::getArticleRoute($vars['id'] . ':' . $vars['alias'], $vars['catid'], $vars['language']);
    }

    public function getResourceKey(string $option, string $view): string
    {
        if ($option !== 'com_content') {
            return '';
        }

        if ($view !== 'article') {
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

        return 'com_content.article.'.$id;
    }
}
