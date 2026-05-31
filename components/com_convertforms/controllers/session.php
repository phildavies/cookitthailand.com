<?php

/**
 * @package         Convert Forms
 * @version         5.0.0 Free
 * 
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2025 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Response\JsonResponse;
use Joomla\CMS\MVC\Controller\BaseController;

class ConvertFormsControllerSession extends BaseController
{
    /**
     * The main submit method
     *
     * @return void
     */
    public function session()
    {  
        ConvertForms\Validation\Helper::run('onInit');

        $app = Factory::getApplication();

        $data = $app->getSession()->get('convertforms');

        echo new JsonResponse($data);

        $app->close();
    }
}