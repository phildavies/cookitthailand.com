<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Site\Controller;

use Firecoders\Component\Route66\Administrator\Helper\HashHelper;
use Firecoders\Component\Route66\Administrator\Helper\UriHelper;
use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Tobscure\JsonApi\Exception\InvalidParameterException;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class UriController extends BaseController
{
    public function build()
    {
        if ($this->input->getCmd('format') !== 'json') {
            throw new Exception\ResourceNotFound(Text::_('JLIB_APPLICATION_ERROR_RECORD'), 404);
        }

        $data = $this->input->get('data', json_decode($this->input->json->getRaw(), true), 'array');

        if (!$this->checkHash($data)) {
            throw new NotAllowed(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
        }

        PluginHelper::importPlugin('route66');
        $route = Factory::getApplication()->triggerEvent('onRoute66AnalyzerUrl', $data);

        $uri = Route::link('site', $route, false, Route::TLS_IGNORE, true);

        $link = substr($uri, \strlen(Uri::root(false)));

        if (str_starts_with($link, 'index.php/')) {
            $link = substr($link, 10);
        }

        $title       = '';
        $description = '';

        $menu  = Factory::getApplication()->getMenu();
        $items = $menu->getItems(['route'], [$link]);

        if (\count($items)) {
            $params      = $items[0]->getParams();
            $title       = $params->get('page_title', '');
            $description = $params->get('menu-meta_description', '');
        }

        echo json_encode(['uri' => $uri, 'title' => $title, 'description' => $description], JSON_UNESCAPED_UNICODE);

        return $this;
    }


    public function parse()
    {
        if ($this->input->getCmd('format') !== 'json') {
            throw new Exception\ResourceNotFound(Text::_('JLIB_APPLICATION_ERROR_RECORD'), 404);
        }

        $data = $this->input->get('data', json_decode($this->input->json->getRaw(), true), 'array');

        if (!$this->checkHash($data)) {
            throw new NotAllowed(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
        }

        if (!isset($data['uri']) || !$data['uri']) {
            throw new InvalidParameterException(Text::_('COM_ROUTE66_ERROR_BAD_REQUEST'), 400);
        }

        $query = UriHelper::parse($data['uri']);

        $response = ['query' => $query];

        echo json_encode($response, JSON_UNESCAPED_UNICODE);

        return $this;
    }

    protected function checkHash($data): bool
    {
        if (!isset($data['hash'])) {
            return false;
        }

        if (!$data['hash']) {
            return false;
        }

        return HashHelper::checkHash($data['hash'], $data);
    }
}
