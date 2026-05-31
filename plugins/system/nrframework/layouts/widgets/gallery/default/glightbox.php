<?php

/**
 * @package         Google Structured Data
 * @version         6.2.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\HTML\HTMLHelper;

extract($displayData);

if (!$readonly && !$disabled)
{
    HTMLHelper::stylesheet('plg_system_nrframework/vendor/glightbox.min.css', ['relative' => true, 'version' => 'auto']);
    HTMLHelper::script('plg_system_nrframework/vendor/glightbox.min.js', ['relative' => true, 'version' => 'auto']);
}