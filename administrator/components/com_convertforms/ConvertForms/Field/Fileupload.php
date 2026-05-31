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

namespace ConvertForms\Field;

defined('_JEXEC') or die('Restricted access');

use ConvertForms\Helper;
use Tassos\Framework\File;
use Tassos\Framework\Mimes;
use Tassos\Framework\SmartTags\SmartTags;
use Tassos\Framework\FileUpload as FrameworkFileUpload;
use Joomla\Registry\Registry;
use Joomla\CMS\Language\Text;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Utility\Utility;

class FileUpload extends \ConvertForms\Field
{
	/**
	 * The default upload folder
	 *
	 * @var string
	 */
	protected $default_upload_folder = '/media/com_convertforms/uploads/{randomid}_{file.basename}';

	/**
	 *  Remove common fields from the form rendering
	 *
	 *  @var  mixed
	 */
	protected $excludeFields = [
		'size',
		'value',
		'browserautocomplete',
		'placeholder',
		'inputcssclass'
	];

	/**
	 *  Set field object
	 *
	 *  @param  mixed  $field  Object or Array Field options
	 */
	public function setField($field)
	{
		parent::setField($field);

		$field = $this->field;

		if (!isset($field->limit_files)) 
		{
			$field->limit_files = 1;
		}

		if (!isset($field->upload_types) || empty($field->upload_types)) 
		{
			$field->upload_types = 'image/*';
		}

		// Accept multiple values
		if ((int) $field->limit_files != 1)
		{
			$field->input_name .= '[]';
		}
	
		return $this;
	}

	/**
	 *  Validate field value
	 *
	 *  @param   mixed  $value           The field's value to validate
	 *
	 *  @return  mixed                   True on success, throws an exception on error
	 */
	public function validate(&$value)
	{
		$is_required 	   = $this->field->get('required');
		$max_files_allowed = $this->field->get('limit_files', 1);
		$allowed_types     = $this->field->get('upload_types');
		$upload_folder     = $this->field->get('upload_folder_type', 'auto') == 'auto' ? $this->default_upload_folder : $this->field->get('upload_folder', $this->default_upload_folder);

		// Remove null and empty values
		$value = is_array($value) ? $value : (array) $value;
		$value = array_filter($value);

		// We expect a not empty array
		if ($is_required && empty($value))
		{
			$this->throwError(Text::_('COM_CONVERTFORMS_FIELD_REQUIRED'));
		}

		// Do we have the correct number of files?
		if ($max_files_allowed > 0 && count($value) > $max_files_allowed)
		{
			$this->throwError(Text::sprintf('COM_CONVERTFORMS_UPLOAD_MAX_FILES_LIMIT', $max_files_allowed));
		}

		// Validate file paths
		foreach ($value as $key => &$source_file)
		{
			// Resolve the submitted reference (file_token or legacy base64 path)
			// to an absolute path inside the temp folder. The framework's
			// FileUpload resolves tokens first and falls back to legacy decoding.
			try
			{
				$source_file = FrameworkFileUpload::resolveReference($source_file, [File::getTempFolder()]);
			}
			catch (\Throwable $th)
			{
				$this->throwError(Text::_('COM_CONVERTFORMS_UPLOAD_FILE_IS_MISSING'));
			}

			$source_file_info = File::pathinfo($source_file);
			$source_basename = $source_file_info['basename'];

			if (!file_exists($source_file))
			{
				$this->throwError(Text::sprintf('COM_CONVERTFORMS_UPLOAD_FILE_IS_MISSING', $source_basename));
			}

			// Although the file is already checked during upload, make another sanity check here. 
			File::checkMimeOrDie($allowed_types, ['tmp_name' => $source_file]);

			// Remove the random ID added to file's name during upload process
			$source_file_info['filename'] = preg_replace('#cf_(.*?)_(.*?)#', '', $source_file_info['filename']);
			$source_file_info['basename'] = preg_replace('#cf_(.*?)_(.*?)#', '', $source_basename);
			$source_file_info['index'] = $key + 1;

			// Replace Smart Tags in the upload folder value
			// Unfortunately at this time, we don't have submitted data available yet and so, we can't replace field-based Smart Tags. See @todo below.
			$SmartTags = new SmartTags();
			$SmartTags->add($source_file_info, 'file.');
			$destination_file = JPATH_ROOT . DIRECTORY_SEPARATOR . $SmartTags->replace($upload_folder);

			// If the upload folder has no filename component, append the source filename.
			// Trailing slash is checked explicitly because pathinfo() reports a phantom extension
			// when a literal Smart Tag containing a dot is the last segment (eg. {field.category}/).
			$is_folder_path = substr($destination_file, -1) === DIRECTORY_SEPARATOR || substr($destination_file, -1) === '/';
			$destination_file_info = File::pathinfo(rtrim($destination_file, '/' . DIRECTORY_SEPARATOR));
			if ($is_folder_path || !isset($destination_file_info['extension']))
			{
				$destination_file = implode(DIRECTORY_SEPARATOR, [$destination_file_info['dirname'], $destination_file_info['basename'], $source_basename]);
			}

			// Move uploaded file to the destination folder after the form passes all validation checks.
			// Thus, if an error is triggered by another field, the file will remain in the temp folder and the user will be able to re-submit the form.
			$this->app->registerEvent('onConvertFormsSubmissionBeforeSave', function(&$data) use ($key, $source_file, $destination_file)
			{
				try
				{
					set_time_limit(300); // 5 Minutes
					ini_set('memory_limit', '-1');
					
					// get the data
					$tmpData = $data;
					$tmpData = $data->getArgument('0');

					// This is a temporary workaround to support field-based Smart Tags in the upload folder
					// @todo 1: We need to prepare $data with ConvertFormsModelConversion->prepare() and pass it down to Submission::replaceSmartTags() in order for submitted values to be prepared with each field prepare() method.
					// @todo 2: Do Smart Tags replacement once. Merge previous replacement for file Smart Tags with this one.
					$SmartTags = new SmartTags();
					$SmartTags->add($tmpData['params'], 'field.');
					$destination_file = $SmartTags->replace($destination_file);

					// Move uploaded file from the temp folder to the destination folder.
					$destination_file = File::move($source_file, $destination_file);

					// Add field name to the tmpData array
					$tmpData['field_name'] = $this->field->get('name');

					

					// Give a chance to manipulate the file with a hook.
					// We can move the file to another folder, rename it, resize it or even uploaded it to a cloud storage service.
					$this->app->triggerEvent('onConvertFormsFileUpload', [&$destination_file, $tmpData]);
					
					// Always save the relative path to the database.
					$destination_file = Helper::pathTorelative($destination_file);

					// Update fields' value
					$tmpData['params'][$this->field->get('name')][$key] = $destination_file;
					
					// Set back the new value to $data object
					$data->setArgument(0, $tmpData);

				} catch (\Throwable $th)
				{
					$this->throwError($th->getMessage());
				}
			});
		}
	}

	

	/**
	 * Event fired before the field options form is rendered in the backend
	 *
	 * @param  object $form
	 *
	 * @return void
	 */
	protected function onBeforeRenderOptionsForm($form)
	{
		// Set the maximum upload size limit to the respective options form field
		$max_upload_size_str = HTMLHelper::_('number.bytes', Utility::getMaxUploadSize());
		$max_upload_size_int = (int) $max_upload_size_str;

		$form->setFieldAttribute('max_file_size', 'max', $max_upload_size_int);

		$desc_lang_str = $form->getFieldAttribute('max_file_size', 'description');
		$desc = Text::sprintf($desc_lang_str, $max_upload_size_str);
		$form->setFieldAttribute('max_file_size', 'description', $desc);
	}

	/**
	 * Ajax method triggered by System Plugin during file upload.
	 *
	 * @param	string	$form_id
	 * @param	string	$field_key
	 *
	 * @return	array
	 */
	public function onAjax($form_id, $field_key)
	{
		$action = $this->app->input->get('action', null, '');
		if (!$action)
		{
			$this->uploadDie('COM_CONVERTFORMS_UPLOAD_ERROR_INVALID_ACTION');
		}

		$allowedActions = ['upload', 'delete'];
		if (!in_array($action, $allowedActions))
		{
			$this->uploadDie('COM_CONVERTFORMS_UPLOAD_ERROR_INVALID_ACTION');
		}

		switch ($action)
		{
			case 'upload':
				$this->actionUpload($form_id, $field_key);
				return;

			case 'delete':
				$this->actionDelete($form_id, $field_key);
				return;
		}
	}

	private function actionUpload($form_id, $field_key)
	{
		if (!$form_id || !$field_key)
		{
			$this->uploadDie('COM_CONVERTFORMS_UPLOAD_ERROR');
		}

		if (!$settings = \ConvertForms\Form::getFieldSettingsByKey($form_id, $field_key))
		{
			$this->uploadDie('COM_CONVERTFORMS_UPLOAD_ERROR_INVALID_FIELD');
		}

		FrameworkFileUpload::handleUpload(new Registry([
			'upload_types'    => $settings->get('upload_types'),
			'allow_unsafe'    => (bool) $settings->get('allow_unsafe', false),
			'filename_prefix' => 'cf_',
			'token_context'   => [
				'form_id'   => (string) $form_id,
				'field_key' => (string) $field_key,
			],
		]));
	}

	private function actionDelete($form_id, $field_key)
	{
		FrameworkFileUpload::handleDelete(new Registry([
			'allowed_bases'    => [File::getTempFolder()],
			'expected_context' => [
				'form_id'   => (string) $form_id,
				'field_key' => (string) $field_key,
			],
		]));
	}

	/**
	 * DropzoneJS detects errors based on the response error code.
	 *
	 * @param  string $error_message
	 *
	 * @return void
	 */
	private function uploadDie($error_message)
	{
		http_response_code('500');
		die(Text::_($error_message));
	}

	/**
	 * Prepare value to be displayed to the user as plain text
	 *
	 * @param  mixed $value
	 *
	 * @return string
	 */
	public function prepareValue($value)
	{
		if (!$value)
		{
			return;
		}

		$value = (array) $value;

		foreach ($value as &$link)
		{
			$link = Helper::absURL($link);
		}

		return implode(', ', $value);
	}

	/**
	 * Prepare value to be displayed to the user as HTML/text
	 *
	 * @param  mixed $value
	 *
	 * @return string
	 */
	public function prepareValueHTML($value)
	{
		if (!$value)
		{
			return;
		}

		$links = (array) $value;
		$value = '';

		foreach ($links as $link)
		{
			$link = Helper::absURL($link);
			$value .= '<div><a download href="' . $link . '">' . File::pathinfo($link)['basename'] . '</a></div>';
		}

		return '<div class="cf-links">' . $value . '</div>';
	}

	/**
	 *  Display a text before the form options
	 *
	 * 	@param   object  $form
	 * 
	 *  @return  string  The text to display
	 */
	protected function getOptionsFormHeader($form)
	{
		$html = '';

		$temp_folder = File::getTempFolder();

		if (!@is_writable($temp_folder))
		{
			$html .= '
				<div class="alert alert-danger">
					' . Text::sprintf('COM_CONVERTFORMS_FILEUPLOAD_TEMP_FOLDER_NOT_WRITABLE', $temp_folder) . '
				</div>
			';
		}

		// Check if the Fileinfo PHP extension is installed required to detect the mime type.
		if (!extension_loaded('fileinfo') || !function_exists('mime_content_type'))
		{
			$html .= '
				<div class="alert alert-danger">
					' . Text::sprintf('COM_CONVERTFORMS_FILEUPLOAD_MIME_CONTENT_TYPE_MISSING') . '
				</div>
			';
		}

		return $html;
	}

	/**
	 * Event fired during form saving in the backend to help us validate user options.
	 *
	 * @param  object	$model			The Form Model
	 * @param  array	$form_data		The form data to be saved
	 * @param  array	$field_options	The field data
	 *
	 * @return bool
	 */
	public function onBeforeFormSave($model, $form_data, &$field_options)
	{
		$allowedFileTypes = array_filter(array_map('trim', explode(',', $field_options['upload_types'])));

		// Ensure the user has entered at least 1 file extension
		if (empty($allowedFileTypes))
		{
            $model->setError(Text::sprintf('COM_CONVERTFORMS_FILEUPLOAD_EMPTY_FILE_TYPES_NOT_ALLOWED'));
			return false;
		}

		// We do not allow empty file extensions
		if (in_array('.', $allowedFileTypes))
		{
            $model->setError(Text::sprintf('COM_CONVERTFORMS_FILEUPLOAD_EMPTY_FILE_EXTENSION_NOT_ALLOWED'));
			return false;
		}

		return parent::onBeforeFormSave($model, $form_data, $field_options);
	}
}