<?php

/**
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2024 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

// No direct access to this file
defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\TextField;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Utility\Utility;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Tassos\Framework\File;
use Tassos\Framework\FileUpload;

class JFormFieldTFFileUpload extends TextField
{
    /**
     *  Method to render the input field
     *
     *  @return  string
     */
    protected function getInput()
    {
        $ajax_url = isset($this->element['ajax_url']) ? (string) $this->element['ajax_url'] : null;
        if (!$ajax_url)
        {
            return Text::_('NR_FILE_UPLOAD_AJAX_URL_NOT_SET');
        }

        $this->assets();

        $limit_files = (int) $this->element['limit_files'] ?? 5;

        $dataAttributes = isset($this->element['data_attributes']) ? json_decode((string) $this->element['data_attributes'], true) : [];

        $max_file_size = (int) $this->element['max_file_size'];
        if ($max_file_size === 0)
        {
            $max_file_size = Utility::getMaxUploadSize() / 1024 / 1024;
        }

        $preview = (string) $this->element['preview'] === '1';

        $class = $limit_files !== 1 ? 'multiple' : '';

        if (!$preview)
        {
            $class .= ' no-preview';
        }

        $payload = [
            'id'                => $this->id,
            'name'              => $this->name,
            'preview'           => $preview,
            'max_file_size'     => $max_file_size,
            'limit_files'       => $limit_files,
            'upload_types'      => (string) $this->element['upload_types'] ?? '*',
            'class'             => $class,
            'ajax_url'          => $ajax_url,
            'dataAttributes'    => $dataAttributes,
            'value'             => $this->value
        ];

        Text::script('NR_FILE_UPLOAD_FALLBACK_MESSAGE');
        Text::script('NR_FILE_UPLOAD_INVALID_FILE');
        Text::script('NR_FILE_UPLOAD_FILETOOBIG');
        Text::script('NR_FILE_UPLOAD_RESPONSE_ERROR');
        Text::script('NR_CANCEL_UPLOAD');
        Text::script('NR_FILE_UPLOAD_CANCEL_UPLOAD_CONFIRMATION');
        Text::script('NR_REMOVE_FILE');
        Text::script('NR_FILE_UPLOAD_MAX_FILES_EXCEEDED');
        Text::script('NR_ARE_YOU_SURE_YOU_WANT_TO_DELETE_THIS_ITEM');
        Text::script('NR_FILE_UPLOAD_FILE_MISSING');
        
        $layout = new FileLayout('fileupload', JPATH_PLUGINS . '/system/nrframework/layouts');
        return $layout->render($payload);
    }

    /**
     * Back-compat shim for older CF, ACF, SP, versions that still call into this field's AJAX entry points directly.
     * Delegates to the FileUpload with safe defaults: uploads land in
     * the framework temp folder and the response includes the legacy
     * file/file_encode props that older JS clients expect.
     *
     * @param   Registry  $options
     *
     * @return  void  Always exits via FileUpload::handleUpload.
     *
     * @deprecated  Use Tassos\Framework\FileUpload::handleUpload() directly.
     */
    public function onUpload($options)
    {
        FileUpload::handleUpload(new Registry([
            'upload_folder'   => null, // null => File::getTempFolder()
            'upload_types'    => $options->get('upload_types', '*'),
            'allow_unsafe'    => false,
            'filename_prefix' => null,
            'filename_suffix' => false,
            'expose_url'      => true,
            // Default true — older clients still read file/file_encode.
            'emit_legacy'     => true,
        ]));
    }

    /**
     * Back-compat shim for pre-3.1.4 ACF's caller: acfupload.php -> taskDelete()
     *
     * Defense: this is a generic, AJAX-reachable delete entry point, so the
     * allow-list is intentionally narrow.
     * 
     *  - Joomla tmp
     *  - media/acfupload
     *  - media/acfupload/tmp
     *  - Directories of every file currently stored in
     *    #__fields_values for acfupload fields
     *
     * Everything else is rejected by FileUpload::handleDelete.
     *
     * @return  void
     *
     * @deprecated  Use Tassos\Framework\FileUpload::handleDelete() directly.
     */
    public function onDelete()
    {
        $bases = [
            File::getTempFolder(),
            Path::clean(JPATH_ROOT . '/media/acfupload'),
            Path::clean(JPATH_ROOT . '/media/acfupload/tmp'),
        ];

        // Old clients (pre-3.1.4 ACF) don't send a field_id with
        // delete requests, so we cannot scope the allowlist to one field.
        // Scan every acfupload field's persisted values instead.
        $bases = array_merge($bases, self::resolveAcfUploadStoredBases());

        FileUpload::handleDelete(new Registry([
            'allowed_bases' => $bases,
        ]));
    }

    /**
     * Directories of every file persisted across every acfupload field,
     * read from #__fields_values. Used as the dynamic part of onDelete()'s
     * allowlist so it tracks where files actually live on disk instead of
     * re-resolving each field's configured upload_folder (whose Smart Tags
     * can drift).
     *
     * @return  string[]
     */
    private static function resolveAcfUploadStoredBases()
    {
        $db    = Factory::getContainer()->get(\Joomla\Database\DatabaseInterface::class);
        $query = $db->getQuery(true)
            ->select($db->quoteName('fv.value'))
            ->from($db->quoteName('#__fields_values', 'fv'))
            ->join(
                'INNER',
                $db->quoteName('#__fields', 'f')
                . ' ON ' . $db->quoteName('fv.field_id') . ' = ' . $db->quoteName('f.id')
            )
            ->where($db->quoteName('f.type') . ' = ' . $db->quote('acfupload'))
            ->where($db->quoteName('f.state') . ' = 1');

        $rows = $db->setQuery($query)->loadColumn();

        $bases = [];

        foreach ((array) $rows as $row)
        {
            if (!$row)
            {
                continue;
            }

            $decoded = json_decode($row, true);
            if (!is_array($decoded))
            {
                continue;
            }

            // Storage shape: either a single {value,...} object or a list of such objects.
            $entries = isset($decoded['value']) ? [$decoded] : $decoded;

            foreach ($entries as $entry)
            {
                $rel = is_array($entry) ? (isset($entry['value']) ? (string) $entry['value'] : '') : (string) $entry;
                if ($rel === '')
                {
                    continue;
                }

                $full = Path::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . ltrim($rel, '/\\'));
                $bases[dirname($full)] = true;
            }
        }

        return array_keys($bases);
    }

    private function assets()
    {
        $wa = Factory::getApplication()->getDocument()->getWebAssetManager();
        
        // Load Sortable
        $limit_files = (int) $this->element['limit_files'] ?? 5;
        if ($limit_files !== 1)
        {
            $wa->registerScript('plg_system_nrframework.sortable', 'plg_system_nrframework/vendor/sortable.min.js', []);
            $wa->useScript('plg_system_nrframework.sortable');
        }

        // Register framework's dropzone vendor library
        $wa->registerScript('plg_system_nrframework.dropzone', 'plg_system_nrframework/vendor/dropzone.min.js', [], ['defer' => true], []);
        
        // Register framework's fileupload CSS
        $wa->registerStyle('plg_system_nrframework.fileupload.css', 'plg_system_nrframework/fileupload.css', [], [], []);
        
        // Register framework's fileupload instance with dropzone dependency
        $wa->registerScript(
            'plg_system_nrframework.fileupload.instance', 
            'plg_system_nrframework/fileupload/instance.js', 
            [], 
            [], 
            ['plg_system_nrframework.dropzone']  // Dependency on dropzone
        );
        
        // Register framework's fileupload JS with dropzone dependency
        $wa->registerScript(
            'plg_system_nrframework.fileupload.initiator', 
            'plg_system_nrframework/fileupload/initiator.js', 
            [], 
            [], 
            ['plg_system_nrframework.fileupload.instance']  // Dependency on dropzone
        );
        
        // Load all assets
        $wa->useScript('plg_system_nrframework.dropzone');
        $wa->useStyle('plg_system_nrframework.fileupload.css');
        $wa->useScript('plg_system_nrframework.fileupload.instance');
        $wa->useScript('plg_system_nrframework.fileupload.initiator');
    }
}