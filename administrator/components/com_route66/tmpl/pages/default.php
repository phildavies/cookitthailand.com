<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Helper\AnalyzerHelper;
use Firecoders\Component\Route66\Administrator\Helper\CrawlerHelper;
use Firecoders\Component\Route66\Administrator\Helper\IssuesHelper;
use Firecoders\Component\Route66\Administrator\Helper\Route66Helper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive');
$wa->useScript('table.columns');
$wa->useScript('multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

Text::script('COM_ROUTE66_CRAWL_RUNNING');
Text::script('COM_ROUTE66_CRAWL_COMPLETED');
?>
<form action="<?php echo Route::_('index.php?option=com_route66&view=pages'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">

            <?php if ($this->crawlerTask): ?>
                <?php if (CrawlerHelper::isRunning($this->crawlerTask)): ?>
                    <?php echo LayoutHelper::render('crawler.running', ['task' => $this->crawlerTask]); ?>
                <?php else: ?>
                    <?php echo LayoutHelper::render('crawler.incomplete', ['task' => $this->crawlerTask]); ?>
                <?php endif; ?>
            <?php endif; ?>

            <div id="j-main-container" class="j-main-container">
                <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
                <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                <?php else : ?>
                    <table class="table table-responsive" id="pagesList">
                        <caption class="visually-hidden">
                            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                        </caption>
                        <thead>
                            <tr>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'page.title', $listDirn, $listOrder); ?>
                                </th>
                                 <th scope="col">
                                    <?php echo Text::_('COM_ROUTE66_ISSUES'); ?>
                                </th>
                                <th scope="col" class="w-1 d-none d-md-table-cell text-center">
                                    <?php echo Text::_('COM_ROUTE66_SEO'); ?>
                                </th>
                                <th scope="col" class="w-1 d-none d-md-table-cell text-center">
                                    <?php echo Text::_('COM_ROUTE66_READABILITY'); ?>
                                </th>
                                 <th scope="col" class="w-1">
                                    <?php echo Text::_('COM_ROUTE66_STATUS'); ?>
                                </th>
                                <th scope="col" class="w-1 d-none d-md-table-cell text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_ROUTE66_PAGE_SIZE_KB', 'page.size', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-1 d-none d-md-table-cell text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_ROUTE66_PAGE_TIME_MS', 'page.time', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-1 d-none d-md-table-cell text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'COM_ROUTE66_CRAWLED', 'page.crawled', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="w-1 d-none d-md-table-cell text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'page.id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($this->items as $i => $item) : ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td>
                                        <?php if ($item->http_status === 200): ?>
                                        <a href="<?php echo $item->editLink; ?>"><?php echo $this->escape($item->title); ?></a>
                                        <?php else: ?>
                                        <span><?php echo $this->escape($item->title); ?></span>
                                        <?php endif; ?>
                                        <div class="small mt-1 text-break"><a target="_blank" href="<?php echo $item->url; ?>"><?php echo $item->link; ?></a></div>
                                    </td>
                                    <td>
                                        <?php echo $item->http_status === 200 ? LayoutHelper::render('pages.issues', ['issues' => IssuesHelper::check($item)]) : ''; ?>
                                    </td>
                                    <td class="small d-none d-md-table-cell text-center">
                                         <?php echo $item->http_status === 200 ? LayoutHelper::render('pages.score', ['rating' => AnalyzerHelper::scoreToRating($item->seo_score)]) : ''; ?>
                                    </td>
                                    <td class="small d-none d-md-table-cell text-center">
                                        <?php echo $item->http_status === 200 ? LayoutHelper::render('pages.score', ['rating' => AnalyzerHelper::scoreToRating($item->readability_score)]) : ''; ?>
                                    </td>
                                    <td class="small d-none d-md-table-cell text-center">
                                         <?php echo $item->http_status; ?>
                                    </td>
                                    <td class="small d-none d-md-table-cell text-center">
                                        <?php echo round($item->size / 1024); ?>
                                    </td>
                                    <td class="small d-none d-md-table-cell text-center">
                                        <?php echo $item->time; ?>
                                    </td>
                                    <td class="small d-none d-md-table-cell text-center">
                                        <?php echo HTMLHelper::_('date', $item->crawled, Text::_('DATE_FORMAT_LC2')); ?>
                                    </td>
                                    <td class="small d-none d-md-table-cell text-center">
                                        <?php echo $item->id; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <?php echo $this->pagination->getListFooter(); ?>

                <?php endif; ?>

                <?php echo $this->filterForm->renderControlFields(); ?>
            </div>
        </div>
    </div>

    <?php echo Route66Helper::copyrights(); ?>
</form>
<script>

const enableToolbarButtons = () => {
    document.querySelector('#toolbar-crawl button').disabled = false;
    document.querySelector('#toolbar-delete button').disabled = false;
    document.querySelector('#toolbar-delete1 button').disabled = false;
}

const disableToolbarButtons = () => {
    document.querySelector('#toolbar-crawl button').disabled = true;
    document.querySelector('#toolbar-delete button').disabled = true;
    document.querySelector('#toolbar-delete1 button').disabled = true;
}

const crawl = async (id) => {

        const url = Joomla.getOptions('system.paths').rootFull + 'administrator/index.php?option=com_route66&task=crawler.crawl&format=json';

        const data = new FormData();
        
        data.append('id', id);
        data.append(window.Joomla.getOptions('csrf.token'), 1);

        try {

            if(!id) {
                Joomla.renderMessages({'info': [Joomla.Text._('COM_ROUTE66_CRAWL_RUNNING')]});
            }

            const response = await fetch(url, {
                method: 'POST',
                body: data,
            });

            if (!response.ok) {
                Joomla.renderMessages({'error': [`Response status: ${response.status}`]});
                return;
            }

            const json = await response.json();

            if(!json.success) {
                Joomla.renderMessages({'error': [json.message]});
                return;
            }

            if(json.data.completed) {
                Joomla.renderMessages({'success': [Joomla.Text._('COM_ROUTE66_CRAWL_COMPLETED')]});
                window.setTimeout(() => {
                    window.location.href = Joomla.getOptions('system.paths').rootFull + 'administrator/index.php?option=com_route66&view=pages';
                }, 2000);
                return;
            }

            crawl(json.data.id);
            
        } catch (error) {
            Joomla.renderMessages({'error': [error.message]});
            enableToolbarButtons();
        }
};

const form = document.querySelector('#adminForm');
const task = document.querySelector('input[name="task"]');
form.addEventListener('submit', (event) => {
    if(task.value === 'pages.crawl') {
        event.preventDefault();
        disableToolbarButtons();
        crawl(0);
    }
});
</script>