<?php

/**
 * @package         Google Structured Data
 * @version         6.2.0 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2026 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace GSD\Schemas\Schemas;

// No direct access
defined('_JEXEC') or die;

use GSD\Helper;

class Organization extends \GSD\Schemas\Base
{
    /**
     * Return all the schema properties
     *
     * @return void
     */
    protected function initProps()
    {
        $logo = Helper::cleanImage(Helper::absURL($this->data->get('logo', '')));
        
        $props = [
            'logo' => $logo,
            'image' => $logo
        ];

        $this->data->loadArray($props);

        parent::initProps();
    }
}