<?php

/**
 * @package         Convert Forms
 * @version         5.1.6 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Router\Route;
use Joomla\CMS\Factory;

extract($displayData);

include_once JPATH_PLUGINS . '/system/nrframework/fields/tffileupload.php';

// Load ConvertForms file upload JavaScript with dependency on framework JavaScript
$wa = Factory::getApplication()->getDocument()->getWebAssetManager();

// Register and use ConvertForms fileupload JS with framework dependency
$wa->registerScript(
    'com_convertforms.fileupload',
    'com_convertforms/field_fileupload.js',
    [],
    [],
    ['plg_system_nrframework.fileupload.instance']  // Dependency on framework JS
);
$wa->useScript('com_convertforms.fileupload');

$value = isset($field->value) ? $field->value : null;

$_field = new \JFormFieldTFFileUpload;

$element = new \SimpleXMLElement('
	<field
		id="' . $field->input_id . '"
		name="' . $field->input_name . '"
		preview="' . (isset($field->layout) && $field->layout === 'thumbnail') . '"
		max_file_size="' . ($field->max_file_size ?? 10) . '"
		limit_files="' . ($field->limit_files ?? 5) . '"
		upload_types="' . ($field->upload_types ?? '*') . '"
		ajax_url="' . Route::_('index.php?option=com_convertforms&task=field.ajax&field_type=fileupload') . '"
		type="TFFileUpload"
	/>
');

$_field->setup($element, $value);

echo $_field->__get('input');