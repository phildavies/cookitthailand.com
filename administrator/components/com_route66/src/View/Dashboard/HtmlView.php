<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\View\Dashboard;

use Firecoders\Component\Route66\Administrator\Helper\IssuesHelper;
use Firecoders\Component\Route66\Administrator\Helper\Route66Helper;
use Firecoders\Component\Route66\Administrator\Issues\DuplicateDescriptionIssue;
use Firecoders\Component\Route66\Administrator\Issues\DuplicateResourceIssue;
use Firecoders\Component\Route66\Administrator\Issues\DuplicateTitleIssue;
use Firecoders\Component\Route66\Administrator\Issues\ExcessiveDOMSizeIssue;
use Firecoders\Component\Route66\Administrator\Issues\NoCompressionIssue;
use Firecoders\Component\Route66\Administrator\Issues\NoDescriptionIssue;
use Firecoders\Component\Route66\Administrator\Issues\NoFollowIssue;
use Firecoders\Component\Route66\Administrator\Issues\NoIndexIssue;
use Firecoders\Component\Route66\Administrator\Issues\NoTitleIssue;
use Firecoders\Component\Route66\Administrator\Issues\PageSizeIssue;
use Firecoders\Component\Route66\Administrator\Issues\PageTimeIssue;
use Firecoders\Component\Route66\Administrator\Issues\RobotsTxtBlockedIssue;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class HtmlView extends BaseHtmlView
{
    protected $isPro                               = false;
    protected $goodPages                           = 0;
    protected $pagesWithIssues                     = 0;
    protected $pagesWithPerformanceIssues          = 0;
    protected $totalPages                          = 0;
    protected $totalPagesLink                      = '';
    protected $pagesWithIssuesLink                 = '';
    protected $pagesWithPerformanceIssuesLink      = '';
    protected $issues                              = [];
    protected $seo                                 = [];
    protected $readability                         = [];

    public function display($tpl = null): void
    {

        $this->isPro = Route66Helper::isPro();

        $model                = Factory::getApplication()->bootComponent('com_route66')->getMVCFactory()->createModel('Pages', 'Administrator', ['ignore_request' => true]);
        $model->setState('filter.response_type', 'normal');

        $this->totalPages     = $model->getTotal();
        $this->totalPagesLink = 'index.php?option=com_route66&view=pages&filter[issues][]=&filter[response_type]=normal';

        if (!$this->isPro && $this->totalPages > 30) {
            $this->totalPages = 30;
        }

        $issuesFilters = ['duplicate_description', 'duplicate_resource', 'duplicate_title', 'dom_size', 'uncompressed', 'no_description', 'no_follow', 'no_index', 'no_title', 'large_page', 'slow_page', 'robots_txt_blocked'];

        $model->setState('filter.issues', $issuesFilters);
        $this->pagesWithIssues     = $model->getTotal();
        $this->pagesWithIssuesLink = 'index.php?option=com_route66&view=pages&filter[response_type]=normal&filter[issues][]='.implode('&filter[issues][]=', $issuesFilters);

        $model->setState('filter.issues', ['dom_size', 'uncompressed', 'large_page', 'slow_page']);
        $this->pagesWithPerformanceIssues     = $model->getTotal();
        $this->pagesWithPerformanceIssuesLink = 'index.php?option=com_route66&view=pages&filter[response_type]=normal&filter[issues][]=dom_size&filter[issues][]=uncompressed&filter[issues][]=large_page&filter[issues][]=slow_page';

        $this->goodPages = $this->totalPages - $this->pagesWithIssues;

        foreach (IssuesHelper::get() as $issue) {

            if ($issue instanceof DuplicateDescriptionIssue) {
                $filter = 'duplicate_description';
            } elseif ($issue instanceof DuplicateResourceIssue) {
                $filter = 'duplicate_resource';
            } elseif ($issue instanceof DuplicateTitleIssue) {
                $filter = 'duplicate_title';
            } elseif ($issue instanceof ExcessiveDOMSizeIssue) {
                $filter = 'dom_size';
            } elseif ($issue instanceof NoCompressionIssue) {
                $filter = 'uncompressed';
            } elseif ($issue instanceof NoDescriptionIssue) {
                $filter = 'no_description';
            } elseif ($issue instanceof NoFollowIssue) {
                $filter = 'no_follow';
            } elseif ($issue instanceof NoIndexIssue) {
                $filter = 'no_index';
            } elseif ($issue instanceof NoTitleIssue) {
                $filter = 'no_title';
            } elseif ($issue instanceof PageSizeIssue) {
                $filter = 'large_page';
            } elseif ($issue instanceof PageTimeIssue) {
                $filter = 'slow_page';
            } elseif ($issue instanceof RobotsTxtBlockedIssue) {
                $filter = 'robots_txt_blocked';
            }

            $model->setState('filter.issues', [$filter]);

            $this->issues[] = (object) ['total' => $model->getTotal(), 'title' => $issue->getLabel(), 'type' => $issue->getType(), 'filter' => $filter];
        }

        $model->setState('filter.issues', []);


        $this->seo = [];

        $model->setState('filter.seo_rating', 'good');
        $this->seo[] = (object) ['total' => $model->getTotal(), 'title' => Text::_('COM_ROUTE66_GOOD'), 'type' => 'success', 'filter' => 'good'];

        $model->setState('filter.seo_rating', 'ok');
        $this->seo[] = (object) ['total' => $model->getTotal(), 'title' => Text::_('COM_ROUTE66_OK'), 'type' => 'warning', 'filter' => 'ok'];

        $model->setState('filter.seo_rating', 'bad');
        $this->seo[] = (object) ['total' => $model->getTotal(), 'title' => Text::_('COM_ROUTE66_NEEDS_IMPROVEMENT'), 'type' => 'danger', 'filter' => 'bad'];

        $this->readability = [];

        $model->setState('filter.readability_rating', 'good');
        $this->readability[] = (object) ['total' => $model->getTotal(), 'title' => Text::_('COM_ROUTE66_GOOD'), 'type' => 'success', 'filter' => 'good'];

        $model->setState('filter.readability_rating', 'ok');
        $this->readability[] = (object) ['total' => $model->getTotal(), 'title' => Text::_('COM_ROUTE66_OK'), 'type' => 'warning', 'filter' => 'ok'];

        $model->setState('filter.readability_rating', 'bad');
        $this->readability[] = (object) ['total' => $model->getTotal(), 'title' => Text::_('COM_ROUTE66_NEEDS_IMPROVEMENT'), 'type' => 'danger', 'filter' => 'bad'];


        $this->addToolbar();
        parent::display($tpl);
    }

    protected function addToolbar(): void
    {
        ToolbarHelper::title(Text::_('COM_ROUTE66_MANAGER_DASHBOARD'), ' fa-chart-line');
        $toolbar = Toolbar::getInstance();

        $toolbar->customButton('review')->html('<joomla-toolbar-button><a class="btn btn-success" href="https://extensions.joomla.org/extension/route-66/" target="_blank"> '.Text::_('COM_ROUTE66_LEAVE_A_REVIEW').'</a></joomla-toolbar-button>');

        $user = $this->getCurrentUser();
        if ($user->authorise('core.admin', 'com_route66') || $user->authorise('core.options', 'com_route66')) {
            $toolbar->preferences('com_route66');
        }
    }
}
