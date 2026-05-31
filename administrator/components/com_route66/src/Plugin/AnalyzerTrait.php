<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2024 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Plugin;

use Joomla\Event\Event;

\defined('_JEXEC') or die;

trait AnalyzerTrait
{
    public function onAnalyzerDisplay(Event $event): void
    {
        foreach (self::RESOURCES as $resource) {

            $className = '\\Firecoders\\Plugin\\Route66\\'.ucfirst($this->_name).'\\Analyzer\\'.ucfirst($resource);
            $instance  = new $className();

            if (!$instance->isEditing()) {
                continue;
            }

            $event->setArgument('result', true);
            $event->stopPropagation();
        }

    }

    public function onAnalyzerSave(Event $event): void
    {
        foreach (self::RESOURCES as $resource) {

            $className = '\\Firecoders\\Plugin\\Route66\\'.ucfirst($this->_name).'\\Analyzer\\'.ucfirst($resource);
            $instance  = new $className();

            if (!$instance->isSaving()) {
                continue;
            }

            $event->setArgument('result', true);
            $event->stopPropagation();
        }

    }

    public function onAnalyzerOptions(Event $event): void
    {
        foreach (self::RESOURCES as $resource) {

            $className = '\\Firecoders\\Plugin\\Route66\\'.ucfirst($this->_name).'\\Analyzer\\'.ucfirst($resource);
            $instance  = new $className();

            if (!$instance->isEditing()) {
                continue;
            }

            $options = $instance->getOptions();

            if (\is_array($options) && !empty($options)) {
                $event->setArgument('result', $options);
                $event->stopPropagation();
                break;
            }
        }

    }

    public function onAnalyzerUrl(Event $event): void
    {
        foreach (self::RESOURCES as $resource) {

            $className = '\\Firecoders\\Plugin\\Route66\\'.ucfirst($this->_name).'\\Analyzer\\'.ucfirst($resource);
            $instance  = new $className();

            $route = $instance->getRoute($event->getArguments());

            if ($route) {
                $event->setArgument('result', $route);
                $event->stopPropagation();
                break;
            }
        }
    }

    public function onAnalyzerResourceKey(Event $event): void
    {
        foreach (self::RESOURCES as $resource) {

            $className = '\\Firecoders\\Plugin\\Route66\\'.ucfirst($this->_name).'\\Analyzer\\'.ucfirst($resource);
            $instance  = new $className();

            [$option, $view] = $event->getArguments();

            if (!$option || !$view) {
                $event->setArgument('result', '');
                $event->stopPropagation();
                return;
            }

            $key = $instance->getResourceKey($option, $view);

            if ($key) {
                $event->setArgument('result', $key);
                $event->stopPropagation();
                break;
            }
        }
    }

    public function onAnalyzerResourceId(Event $event): void
    {
        foreach (self::RESOURCES as $resource) {

            $className = '\\Firecoders\\Plugin\\Route66\\'.ucfirst($this->_name).'\\Analyzer\\'.ucfirst($resource);
            $instance  = new $className();

            if (!$instance->isEditing()) {
                continue;
            }

            $resourceId = $instance->getResourceId();

            $event->setArgument('result', $resourceId);
            $event->stopPropagation();
        }
    }
}
