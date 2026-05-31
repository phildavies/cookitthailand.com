<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Field;

use Joomla\CMS\Form\Field\PredefinedlistField;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects


class StatusField extends PredefinedlistField
{
    public $type = 'Status';

    protected $predefinedOptions = [
        1 => 'JPUBLISHED',
        0 => 'JUNPUBLISHED',
    ];
}
