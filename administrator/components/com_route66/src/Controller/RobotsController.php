<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Controller;

use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Versioning\VersionableControllerTrait;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class RobotsController extends FormController
{
    use VersionableControllerTrait;

    protected $text_prefix = 'COM_ROUTE66_ROBOTS';

    public function edit($key = null, $urlVar = null)
    {
        $model  = $this->getModel();
        $table  = $model->getTable();

        if (!$table->load(1)) {
            $table->store();
        }

        $this->input->set('id', 1);

        return parent::edit($key, $urlVar);
    }

    protected function postSaveHook(BaseDatabaseModel $model, $validData = [])
    {
        // Use table instead of validData so versions work
        $table = $model->getTable();

        if (!$table->load(1)) {
            return;
        }

        if (!$table) {
            return;
        }

        if (!$table->contents) {
            return;
        }

        file_put_contents(JPATH_SITE.'/robots.txt', $table->contents);
    }

    public function cancel($key = null)
    {
        $result = parent::cancel($key);

        $this->setRedirect('index.php?option=com_route66');

        return $result;
    }
}
