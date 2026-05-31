<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Field;

use Joomla\CMS\Form\Field\SubformField;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class AnalyzerField extends SubformField
{
    protected $type = 'Analyzer';

    protected $subform = null;

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        if (!parent::setup($element, $value, $group)) {
            return false;
        }

        $this->layout      = 'form.field.analyzer';
        $this->hiddenLabel = true;

        return true;
    }

    protected function getLayoutPaths()
    {
        $paths   = parent::getLayoutPaths();
        $paths[] = JPATH_SITE.'/administrator/components/com_route66/layouts';

        return $paths;
    }
}
