<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Controller;

use Firecoders\Component\Route66\Administrator\Helper\HashHelper;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Uri\Uri;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class UriController extends BaseController
{
    public function build()
    {
        $data = $this->input->get('data', json_decode($this->input->json->getRaw(), true), 'array');

        $data['hash'] = HashHelper::generateHash($data);

        $http     = HttpFactory::getHttp();
        $response = $http->post(Uri::root(false).'index.php?option=com_route66&task=uri.build&format=json', json_encode($data));

        if ($response->code !== 200) {
            throw new \Exception(Text::_('JLIB_APPLICATION_ERROR_RECORD'), 404);
        }

        echo $response->body;

        return $this;
    }
}
