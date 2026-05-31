<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Site\Controller;

use Firecoders\Component\Route66\Administrator\Helper\HashHelper;
use Firecoders\Component\Route66\Administrator\Helper\PageHelper;
use Firecoders\Component\Route66\Administrator\Helper\UriHelper;
use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Uri\Uri;
use Tobscure\JsonApi\Exception\InvalidParameterException;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class PageController extends BaseController
{
    public function discover()
    {
        if ($this->input->getCmd('format') !== 'json') {
            throw new Exception\ResourceNotFound(Text::_('JLIB_APPLICATION_ERROR_RECORD'), 404);
        }

        $data = $this->input->get('data', json_decode($this->input->json->getRaw(), true), 'array');

        if (!$this->checkCsrf($data)) {
            throw new NotAllowed(Text::_('JINVALID_TOKEN_NOTICE'), 403);
        }

        if (!$this->checkHash($data)) {
            throw new NotAllowed(Text::_('JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN'), 403);
        }

        if (!isset($data['uri']) || !$data['uri']) {
            throw new InvalidParameterException(Text::_('COM_ROUTE66_ERROR_BAD_REQUEST'), 400);
        }

        $uri = Uri::getInstance($data['uri']);

        $query = $uri->getQuery(true);

        if (isset($query['return'])) {
            $this->app->setHeader('status', 204);
            return $this;
        }

        $link = UriHelper::getLink($uri);

        $model  = $this->getModel('Page', 'Administrator', ['ignore_request' => true]);
        $page   = $model->getTable();
        $exists = $page->load(['link_hash' => PageHelper::hash($link)]);

        if ($exists) {
            $this->app->setHeader('status', 200);
            return $this;
        }

        PageHelper::fetch($data['uri']);

        $this->app->setHeader('status', 201);

        return $this;
    }

    protected function checkCsrf($data)
    {
        $token = Session::getFormToken();

        return isset($data[$token]) && $data[$token] === 1;
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
