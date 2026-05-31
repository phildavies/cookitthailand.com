<?php

/**
 * Joomla! Content Management System
 *
 * @copyright  (C) 2020 Open Source Matters, Inc. <https://www.joomla.org>
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace Firecoders\Component\Route66\Administrator\Console;

use Firecoders\Component\Route66\Administrator\Crawler\Observer;
use Firecoders\Component\Route66\Administrator\Crawler\Profile;
use Firecoders\Component\Route66\Administrator\Crawler\Queue;
use Firecoders\Component\Route66\Administrator\Helper\CrawlerHelper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\Console\Command\AbstractCommand;
use Joomla\Database\DatabaseAwareTrait;
use Joomla\Database\DatabaseInterface;
use Spatie\Crawler\Crawler;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class CrawlCommand extends AbstractCommand
{
    use DatabaseAwareTrait;

    protected static $defaultName = 'route66:crawl';

    public function __construct(DatabaseInterface $db)
    {
        parent::__construct();

        $this->setDatabase($db);
    }

    protected function configure(): void
    {
        $this->setDescription('Crawls all site URLs');
        $this->setHelp("<info>%command.name%</info> Crawls all site URLs \nUsage: <info>php %command.full_name%</info>");
    }

    protected function doExecute(InputInterface $input, OutputInterface $output): int
    {
        $language = Factory::getLanguage();
        $language->load('com_route66', JPATH_SITE.'/administrator/components/com_route66', 'en-GB');

        $io = new SymfonyStyle($input, $output);

        $params  = ComponentHelper::getParams('com_route66');
        $siteUrl = $params->get('site_url');

        if (!$siteUrl) {
            $io->error(Text::_('COM_ROUTE66_CRAWL_FAILED_NO_SITE_URL'));
            return Command::FAILURE;
        }

        if (\JDEBUG) {
            $io->error(Text::_('COM_ROUTE66_CRAWL_FAILED_DEBUG'));
            return Command::FAILURE;
        }

        $application = Factory::getApplication();
        $mvcFactory  = $application->bootComponent('com_route66')->getMVCFactory();

        $crawlerTaskModel = $mvcFactory->createModel('CrawlerTask', 'Administrator', ['ignore_request' => true]);
        $activeTask       = $crawlerTaskModel->getActiveTask();

        if ($activeTask) {

            if (CrawlerHelper::isRunning($activeTask)) {
                $io->error(Text::_('COM_ROUTE66_CRAWL_TASK_RUNNING'));
                return Command::FAILURE;
            }

            $user     = Factory::getUser($activeTask->created_by);
            $created  = HTMLHelper::_('date', $activeTask->created, Text::_('DATE_FORMAT_LC2'));
            $modified = HTMLHelper::_('date', $activeTask->modified, Text::_('DATE_FORMAT_LC2'));
            $text     = $activeTask->created_by ? Text::sprintf('COM_ROUTE66_INCOMPLETE_CRAWL_TASK_INFO_WEB', $user->name, $created, $modified) : Text::sprintf('COM_ROUTE66_INCOMPLETE_CRAWL_TASK_INFO_CLI', $created, $modified);

            $io->title(Text::_('COM_ROUTE66_INCOMPLETE_CRAWL_TASK'));
            $io->info(Text::_($text));
            $choice = $io->choice(Text::_('COM_ROUTE66_INCOMPLETE_CRAWL_TASK_DETAILS'), [1 => Text::_('COM_ROUTE66_RESUME_TASK'), 0 => Text::_('COM_ROUTE66_DISCARD_TASK')]);

            if ($choice === Text::_('COM_ROUTE66_DISCARD_TASK')) {

                $table = $crawlerTaskModel->getTable();

                if (!$table->delete($activeTask->id)) {
                    $io->error($table->getError());
                    return Command::FAILURE;
                }

                $crawlerTaskModel->crearQueue();

                return Command::SUCCESS;
            }
        }

        $pagesModel = $mvcFactory->createModel('Pages', 'Administrator', ['ignore_request' => true]);
        $pagesModel->purge();

        $io->title('Crawling '.$siteUrl);

        $robotsTxt  = CrawlerHelper::getRobotsTxt();
        $options    = CrawlerHelper::getOptions();
        $profile    = new Profile($siteUrl);
        $observer   = new Observer($robotsTxt, $io);

        if ($activeTask) {
            $id = $activeTask->id;
        } else {
            $crawlerTaskModel->save(['queue' => new Queue()]);
            $id   = $crawlerTaskModel->getState('crawlertask.id');
        }

        $task = $crawlerTaskModel->getItem($id);

        if (!$task) {
            $io->error(Text::_('COM_ROUTE66_CRAWL_FAILED'));
            return Command::FAILURE;
        }

        do {
            $crawler = Crawler::create($options);
            $crawler->setCrawlQueue($task->queue);
            $crawler->setCurrentCrawlLimit(50);
            $crawler->ignoreRobots();
            $crawler->acceptNofollowLinks();
            $crawler->setParseableMimeTypes(['text/html']);
            $crawler->setCrawlProfile($profile);
            $crawler->setCrawlObserver($observer);
            $crawler->setConcurrency((int) $params->get('crawler_concurrency', 10));
            $crawler->startCrawling($siteUrl);

            $task->queue = $crawler->getCrawlQueue();

            $crawlerTaskModel->save(['id' => $task->id, 'queue' => $task->queue, 'state' => 0]);

        } while ($task->queue->hasPendingUrls());

        $crawlerTaskModel->save(['id' => $task->id, 'queue' => $task->queue, 'state' => 1]);
        $crawlerTaskModel->clearQueue();

        $io->success('Completed');

        return Command::SUCCESS;
    }
}
