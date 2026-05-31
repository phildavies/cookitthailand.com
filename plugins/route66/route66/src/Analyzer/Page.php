<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Route66\Route66\Analyzer;

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Analyzer\Resource;
use Joomla\CMS\Factory;

class Page extends Resource
{
    public function isEditing(): bool
    {
        $application = Factory::getApplication();
        $input       = $application->input;

        return $application->isClient('administrator') && $input->getCmd('option') === 'com_route66' && $input->getCmd('view') === 'page' && $input->getCmd('layout') === 'edit';
    }

    public function isSaving(): bool
    {
        $application = Factory::getApplication();
        $input       = $application->input;

        return $application->isClient('administrator') && $input->getCmd('option') === 'com_route66' && $input->getCmd('view') === 'page' && $input->getCmd('task') === 'page.save';
    }

    public function getOptions(): array
    {
        return [
            'fields' => [
                'key'      => '#jform_id',
                'title'    => '#jform_title',
                'text'     => '#jform_text',
                'metadata' => [
                    'title'       => '#jform_title',
                    'description' => '#jform_description',
                ],
                'language' => '#jform_language',
            ],
        ];
    }

    public function getRoute(array $vars): string
    {
        return '';
    }

    public function getResourceKey(string $option, string $view): string
    {
        return '';
    }

    public function getResourceId(): string
    {
        return '';
    }
}
