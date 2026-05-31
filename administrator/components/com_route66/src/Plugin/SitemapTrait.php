<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2024 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Plugin;

use Joomla\Event\Event;

\defined('_JEXEC') or die;

trait SitemapTrait
{
    use ExtensionTrait;
    use ModelTrait;

    public function onSitemapForm(Event $event): void
    {
        [$form] = $event->getArguments();

        $formFile = JPATH_SITE . '/plugins/route66/' . $this->_name . '/forms/sitemap.xml';

        if (!$this->isInstalled() || !is_file($formFile)) {
            return;
        }

        $form->loadFile($formFile);
    }

    public function onSitemapItems(Event $event): void
    {
        if (!$this->isInstalled()) {
            return;
        }

        [$feed, $extension, $offset, $limit] = $event->getArguments();

        if ($this->_name !== $extension) {
            return;
        }

        if (!$feed->sources->get($this->_name)) {
            return;
        }

        $model = $this->getModel('Sitemap');

        if (!$model) {
            return;
        }

        $model->setState('list.start', $offset);
        $model->setState('list.limit', $limit);

        $model->setState('settings', $feed->settings);
        $model->setState('sources', $feed->sources);

        $items = $model->getItems();

        $event->setArgument('result', [$items]);
    }

    public function onSitemapItemsCount(Event $event): void
    {
        if (!$this->isInstalled()) {
            return;
        }

        [$feed] = $event->getArguments();

        if (!$feed->sources->get($this->_name)) {
            return;
        }

        $model = $this->getModel('Sitemap');

        if (!$model) {
            return;
        }

        $model->setState('settings', $feed->settings);
        $model->setState('sources', $feed->sources);

        $result = ['extension' => $this->_name, 'count' => $model->getTotal()];

        $event->setArgument('result', array_merge($event->getArgument('result', []), [$result]));
    }
}
