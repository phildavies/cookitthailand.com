<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

class TitleField extends AITextField
{
    protected $type = 'Title';

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        if (!parent::setup($element, $value, $group)) {
            return false;
        }

        $application      = Factory::getApplication();
        $siteNameInTitles = $application->get('sitename_pagetitles');

        if ($siteNameInTitles == 1) {
            $this->addonBefore = ' - '.$application->get('sitename');
        } elseif ($siteNameInTitles == 2) {
            $this->addonAfter = ' - '.$application->get('sitename');
        }

        return true;
    }
}
