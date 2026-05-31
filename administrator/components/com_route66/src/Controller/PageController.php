<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Controller;

use Firecoders\Component\Route66\Administrator\Helper\HashHelper;
use Firecoders\Component\Route66\Administrator\Helper\PageHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Http\HttpFactory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class PageController extends FormController
{
    protected $text_prefix = 'COM_ROUTE66_PAGE';

    public function fetch()
    {
        $this->checkToken();

        $id = $this->input->getInt('id');

        $model = $this->getModel();
        $page  = $model->getItem($id);

        $this->setRedirect(Route::_('index.php?option=com_route66&view=page&layout=edit&id='.$id, false));

        PageHelper::fetch($page->url, $page->id);

        return true;
    }

    public function get()
    {
        $id = $this->input->getInt('id');

        $model = $this->getModel();
        $page  = $model->getItem($id);

        if (!$page) {
            throw new \Exception(Text::_('JLIB_APPLICATION_ERROR_RECORD'), 404);
        }

        $params  = ComponentHelper::getParams('com_route66');
        $siteUrl = $params->get('site_url', Uri::root(false));
        $url     = rtrim($siteUrl, '/') . $page->link;

        $hash = HashHelper::generateHash(['uri' => $url]);
        $glue = str_contains($url, '?') ? '&' : '?';
        $url .= $glue.$hash.'=1';

        $http     = HttpFactory::getHttp();
        $response = $http->get($url);

        if ($response->code !== 200) {
            throw new \Exception(Text::_('JLIB_APPLICATION_ERROR_RECORD'), 404);
        }

        echo $response->body;

        return $this;
    }

    protected function postSaveHook(BaseDatabaseModel $model, $validData = [])
    {
        $id = (int) $model->getState('page.id');

        if (!$id) {
            return;
        }

        $item = $model->getItem($id);

        if (!$item) {
            return;
        }

        $pageData = ['page_id' => $item->id, 'resource_id' => $item->resource_id, 'link_hash' => $item->link_hash];

        $metadataModel = $this->getModel('Metadata');
        $metadataModel->save(array_merge($validData['metadata'], $pageData));

        $contentAnalysisModel = $this->getModel('ContentAnalysis');
        $contentAnalysisModel->save(array_merge($validData['analysis'], $pageData));
    }
}
