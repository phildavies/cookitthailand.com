<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Controller;

use Firecoders\Component\Route66\Administrator\Crawler\Observer;
use Firecoders\Component\Route66\Administrator\Crawler\Profile;
use Firecoders\Component\Route66\Administrator\Crawler\Queue;
use Firecoders\Component\Route66\Administrator\Helper\CrawlerHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\AdminController;
use Joomla\CMS\Response\JsonResponse;
use Spatie\Crawler\Crawler;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class CrawlerController extends AdminController
{
    public function crawl()
    {
        $this->checkToken();

        $params = ComponentHelper::getParams('com_route66');

        if (!$siteUrl = $params->get('site_url')) {
            echo new JsonResponse([], Text::_('COM_ROUTE66_CRAWL_FAILED_NO_SITE_URL'), true);
            return $this;
        }

        if (\JDEBUG) {
            echo new JsonResponse([], Text::_('COM_ROUTE66_CRAWL_FAILED_DEBUG'), true);
            return $this;
        }

        $crawlerTaskModel = Factory::getApplication()->bootComponent('com_route66')->getMVCFactory()->createModel('CrawlerTask', 'Administrator', ['ignore_request' => true]);
        $pagesModel       = Factory::getApplication()->bootComponent('com_route66')->getMVCFactory()->createModel('Pages', 'Administrator', ['ignore_request' => true]);

        $id = $this->input->getInt('id');

        if (!$id) {

            $activeTask = $crawlerTaskModel->getActiveTask();

            if ($activeTask) {
                echo new JsonResponse([], Text::_('COM_ROUTE66_CRAWL_TASK_RUNNING'), true);
                return $this;
            }

            $pagesModel->purge();

            $data = ['queue' => new Queue(), 'state' => 0];
            $crawlerTaskModel->save($data);
            $id = $crawlerTaskModel->getState('crawlertask.id');
        }

        $task = $crawlerTaskModel->getItem($id);

        if (!$task) {
            echo new JsonResponse([], Text::_('COM_ROUTE66_CRAWL_FAILED'), true);
            return $this;
        }

        $robotsTxt  = CrawlerHelper::getRobotsTxt();
        $profile    = new Profile($siteUrl);
        $observer   = new Observer($robotsTxt);
        $limit      = $params->get('total_crawl_limit', 0);

        $options = CrawlerHelper::getOptions();
        $crawler = Crawler::create($options);
        $crawler->setCrawlQueue($task->queue);
        if ($limit) {
            $crawler->setCurrentCrawlLimit(1);
        } else {
            $crawler->setCurrentCrawlLimit(20);
        }
        $crawler->ignoreRobots();
        $crawler->acceptNofollowLinks();
        $crawler->setParseableMimeTypes(['text/html']);
        $crawler->setCrawlProfile($profile);
        $crawler->setCrawlObserver($observer);
        $crawler->setConcurrency((int) $params->get('crawler_concurrency', 10));
        $crawler->startCrawling($siteUrl);

        $task->queue = $crawler->getCrawlQueue();

        if ($limit) {
            $completed = $pagesModel->getTotal() >= $limit || $task->queue->hasPendingUrls() === false;
            if ($completed) {
                Factory::getApplication()->getSession()->set('com_route66.crawl_limit_message_shown', 0);
            }
        } else {
            $completed = $task->queue->hasPendingUrls() === false;
        }

        $data = ['id' => $task->id, 'queue' => $task->queue, 'state' => $completed ? 1 : 0];
        $crawlerTaskModel->save($data);

        if ($completed) {
            $crawlerTaskModel->clearQueue();
        }

        echo new JsonResponse(['id' => $task->id, 'completed' => $completed]);

        return $this;
    }

    public function discard()
    {
        $this->checkToken();

        $model = Factory::getApplication()->bootComponent('com_route66')->getMVCFactory()->createModel('CrawlerTask', 'Administrator', ['ignore_request' => true]);
        $id    = $this->input->getInt('id');

        if (!$id) {
            echo new JsonResponse([], Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), true);
            return $this;
        }

        if (!$model->delete($id)) {
            echo new JsonResponse([], Text::_('JERROR_AN_ERROR_HAS_OCCURRED'), true);
            return $this;
        }

        $model->clearQueue();

        echo new JsonResponse([]);

        return $this;
    }
}
