<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\System\Route66\Extension;

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Helper\AnalyzerHelper;
use Firecoders\Component\Route66\Administrator\Helper\MetadataHelper;
use Firecoders\Component\Route66\Administrator\Helper\PageHelper;
use Firecoders\Component\Route66\Administrator\Helper\PerformanceHelper;
use Firecoders\Component\Route66\Administrator\Helper\Route66Helper;
use Firecoders\Component\Route66\Administrator\Router\Router as Route66Router;
use Joomla\CMS\Event\Application\AfterInitialiseEvent;
use Joomla\CMS\Event\Application\AfterRenderEvent;
use Joomla\CMS\Event\Application\AfterRouteEvent;
use Joomla\CMS\Event\Model\PrepareFormEvent;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Event\SubscriberInterface;

final class Route66 extends CMSPlugin implements SubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return [
            'onAfterInitialise'    => 'onAfterInitialise',
            'onAfterRoute'         => 'onAfterRoute',
            'onAfterRender'        => 'onAfterRender',
            'onBeforeRender'       => 'onBeforeRender',
            'onBeforeCompileHead'  => 'onBeforeCompileHead',
            'onContentPrepareForm' => 'onContentPrepareForm',
        ];
    }

    public function onAfterInitialise(AfterInitialiseEvent $event): void
    {
        $application = $this->getApplication();

        if ($application->get('sef')) {
            new Route66Router();
        }

        Route66Helper::setVersion();
        Route66Helper::setFeatures();
    }

    public function onAfterRoute(AfterRouteEvent $event)
    {
        if (AnalyzerHelper::shouldSave()) {
            AnalyzerHelper::save();
        }
    }

    public function onContentPrepareForm(PrepareFormEvent $event)
    {
        $form = $event->getForm();

        if (!$form) {
            return;
        }

        $application = Factory::getApplication();

        if ($form->getName() === 'com_config.component' && $application->input->getCmd('component') === 'com_route66') {
            Route66Helper::options($form);
            return;
        }

        if (AnalyzerHelper::shouldDisplay()) {
            AnalyzerHelper::display('form', $form);
        }
    }

    public function onBeforeRender()
    {
        if (AnalyzerHelper::shouldDisplay()) {
            AnalyzerHelper::display('toolbar');
        }
    }

    public function onBeforeCompileHead()
    {
        $application = Factory::getApplication();
        $document    = Factory::getDocument();

        if ($application->isClient('site') && $document->getType() === 'html') {
            MetadataHelper::setMetadata();
            PageHelper::discover();
            PerformanceHelper::assets();
        }
    }

    public function onAfterRender(AfterRenderEvent $event)
    {
        PerformanceHelper::optimize();
        PageHelper::extract();
    }

}
