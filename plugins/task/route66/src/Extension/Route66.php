<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Plugin\Task\Route66\Extension;

use Firecoders\Component\Route66\Administrator\Helper\PageHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Date\Date;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Scheduler\Administrator\Event\ExecuteTaskEvent;
use Joomla\Component\Scheduler\Administrator\Task\Status;
use Joomla\Component\Scheduler\Administrator\Traits\TaskPluginTrait;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Event\SubscriberInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

final class Route66 extends CMSPlugin implements SubscriberInterface
{
    use TaskPluginTrait;
    use DatabaseAwareTrait;

    private const TASKS_MAP = [
        'route66' => [
            'langConstPrefix' => 'PLG_TASK_ROUTE66',
            'form'            => 'params',
            'method'          => 'fetch',
        ],
    ];

    protected $autoloadLanguage = true;

    public static function getSubscribedEvents(): array
    {
        return [
            'onTaskOptionsList'    => 'advertiseRoutines',
            'onExecuteTask'        => 'standardRoutineHandler',
            'onContentPrepareForm' => 'enhanceTaskItemForm',
        ];
    }

    private function fetch(ExecuteTaskEvent $event): int
    {
        $params  = ComponentHelper::getParams('com_route66');
        $siteUrl = $params->get('site_url');

        if (!$siteUrl) {
            return Status::OK;
        }

        $siteUrl = rtrim($siteUrl, '/');

        $date   = new Date();
        $date->modify('-15 minutes');

        $db    = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('*')->from($db->qn('#__route66_pages'));
        $query->where($db->qn('crawled') .' < '.$db->q($date->toSql()));
        $query->order($db->qn('crawled'). ' ASC');
        $db->setQuery($query, 0, 20);
        $pages = $db->loadObjectList();

        foreach ($pages as $page) {
            $url = $siteUrl.$page->link;
            PageHelper::fetch($url, $page->id);
        }

        return Status::OK;
    }
}
