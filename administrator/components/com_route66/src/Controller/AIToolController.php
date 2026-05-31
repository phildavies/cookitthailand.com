<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Controller;

use Firecoders\Component\Route66\Administrator\Helper\AIHelper;
use Firecoders\Component\Route66\Administrator\Helper\LanguageHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\Versioning\VersionableControllerTrait;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class AIToolController extends FormController
{
    use VersionableControllerTrait;

    protected $text_prefix = 'COM_ROUTE66_AI_TOOL';

    protected function allowAdd($data = [])
    {
        return $this->app->getIdentity()->authorise('core.manage', 'com_route66');
    }

    protected function allowEdit($data = [], $key = 'id')
    {
        return $this->app->getIdentity()->authorise('core.manage', 'com_route66');
    }

    public function init()
    {
        $this->checkToken();

        $language = LanguageHelper::detectLanguage($this->input->getString('language'));

        $input = [
            'keyphrase' => $this->input->getString('keyphrase', ''),
            'title'     => $this->input->getString('title', ''),
            'text'      => $this->input->getString('text', ''),
            'tone'      => $this->input->getString('tone', ''),
            'language'  => $language,
        ];

        $requestId = uniqid('com_route66_ai_');

        $session = Factory::getApplication()->getSession();
        $session->set($requestId, json_encode($input));

        echo $requestId;

        $this->app->close();
    }

    public function run()
    {
        $this->checkToken('get');

        $id = $this->input->getInt('id');

        if (!$id) {
            throw new \Exception(Text::_('JLIB_APPLICATION_ERROR_RECORD'), 400);
        }

        $model = $this->getModel('AITool', 'Administrator', ['ignore_request' => true]);
        $tool  = $model->getItem($id);

        if (!$tool) {
            throw new \Exception(Text::_('JLIB_APPLICATION_ERROR_RECORD'), 404);
        }

        $requestId = $this->input->getString('request', '');

        if (!$requestId) {
            throw new \Exception(Text::_('JLIB_APPLICATION_ERROR_RECORD'), 400);
        }

        $session = Factory::getApplication()->getSession();

        if (!$session->has($requestId)) {
            throw new \Exception(Text::_('JLIB_APPLICATION_ERROR_RECORD'), 400);
        }

        $json = $session->get($requestId);

        if (\is_null($json)) {
            throw new \Exception(Text::_('JLIB_APPLICATION_ERROR_RECORD'), 400);
        }

        $session->remove($requestId);

        $input = (array) json_decode($json);

        @set_time_limit(120);

        @ini_set('output_buffering', 'off');
        @ini_set('zlib.output_compression', '0');
        @ini_set('implicit_flush', '1');

        header('Content-Type: text/event-stream');
        header('Cache-Control: no-cache, no-transform');
        header('Connection: keep-alive');
        header('X-Accel-Buffering: no');

        while (ob_get_level() > 0) {
            ob_end_clean();
        }

        ob_implicit_flush(true);

        echo "event: start\n";
        echo "data: start\n\n";
        @ob_flush();
        @flush();

        AIHelper::run($tool->id, $input);

        echo "event: done\n";
        echo "data: done\n\n";
        @ob_flush();
        @flush();

        $this->app->close();
    }
}
