<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\PredefinedlistField;

class AIResourcePropertyField extends PredefinedlistField
{
    protected $predefinedOptions = [
        'title'            => 'COM_ROUTE66_RESOURCE_TITLE',
        'text'             => 'COM_ROUTE66_RESOURCE_TEXT',
        'seo_title'        => 'COM_ROUTE66_SEO_TITLE',
        'meta_description' => 'COM_ROUTE66_META_DESCRIPTION',
        'og_title'         => 'COM_ROUTE66_OG_TITLE',
        'og_description'   => 'COM_ROUTE66_OG_DESCRIPTION',
        'x_title'          => 'COM_ROUTE66_X_TITLE',
        'x_description'    => 'COM_ROUTE66_X_DESCRIPTION',
    ];
}
