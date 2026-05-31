<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Helper\Route66Helper;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')->useScript('multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));

$states = [
    0 => [
        'task'           => 'publish',
        'text'           => '',
        'active_title'   => 'JDISABLED',
        'inactive_title' => '',
        'tip'            => true,
        'active_class'   => 'unpublish',
        'inactive_class' => 'unpublish',
    ],
    1 => [
        'task'           => 'unpublish',
        'text'           => '',
        'active_title'   => 'JENABLED',
        'inactive_title' => '',
        'tip'            => true,
        'active_class'   => 'publish',
        'inactive_class' => 'publish',
    ],
];


if ($listOrder === 'aitool.ordering' && !empty($this->items)) {
    $saveOrderingUrl = 'index.php?option=com_route66&task=aitools.saveOrderAjax&tmpl=component&' . Session::getFormToken() . '=1';
    HTMLHelper::_('draggablelist.draggable');
}
?>
<form action="<?php echo Route::_('index.php?option=com_route66&view=aitools'); ?>" method="post" name="adminForm" id="adminForm">
    <div class="row">
        <div class="col-md-12">
            <div id="j-main-container" class="j-main-container">
                    <?php echo LayoutHelper::render('joomla.searchtools.default', ['view' => $this]); ?>
                    <?php if (empty($this->items)) : ?>
                    <div class="alert alert-info">
                        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
                        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
                    </div>
                    <?php else : ?>
                    <table class="table" id="ai-tools-list">
                        <caption class="visually-hidden">
                            <span id="orderedBy"><?php echo Text::_('JGLOBAL_SORTED_BY'); ?> </span>,
                            <span id="filteredBy"><?php echo Text::_('JGLOBAL_FILTERED_BY'); ?></span>
                        </caption>
                        <thead>
                            <tr>
                                <td class="w-1 text-center">
                                    <?php echo HTMLHelper::_('grid.checkall'); ?>
                                </td>
                                <th scope="col" class="w-1 text-center d-none d-md-table-cell">
                                    <?php echo HTMLHelper::_('searchtools.sort', '', 'aitool.ordering', $listDirn, $listOrder, null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-sort'); ?>
                                </th>
                                <th scope="col" class="w-1 text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JSTATUS', 'aitool.state', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGLOBAL_TITLE', 'aitool.title', $listDirn, $listOrder); ?>
                                </th>
                                <th scope="col" class="text-center">
                                    <?php echo Text::_('COM_ROUTE66_AI_TOOL_TYPE'); ?>
                                </th>
                                <th scope="col" class="w-5 d-none d-md-table-cell text-center">
                                    <?php echo HTMLHelper::_('searchtools.sort', 'JGRID_HEADING_ID', 'aitool.id', $listDirn, $listOrder); ?>
                                </th>
                            </tr>
                        </thead>
                        <tbody <?php if ($listOrder === 'aitool.ordering'): ?> class="js-draggable" data-url="index.php?option=com_route66&task=aitools.saveOrderAjax&tmpl=component&<?php echo Session::getFormToken(); ?>=1" data-direction="<?php echo strtolower($listDirn); ?>" <?php endif; ?>>
                            <?php foreach ($this->items as $i => $item) :  ?>
                                <tr class="row<?php echo $i % 2; ?>">
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('grid.id', $i, $item->id, false, 'cid', 'cb', $item->title); ?>
                                    </td>
                                    <td class="text-center d-none d-md-table-cell">
                                        <?php if ($listOrder === 'aitool.ordering') : ?>
                                        <span class="sortable-handler">
                                            <span class="icon-ellipsis-v" aria-hidden="true"></span>
                                        </span>
                                         <input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" class="width-20 text-area-order hidden" />
                                        <?php else: ?>
                                       <span class="sortable-handler inactive" title="<?php echo Text::_('JORDERINGDISABLED'); ?>">
                                            <span class="icon-ellipsis-v" aria-hidden="true"></span>
                                        </span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="text-center">
                                        <?php echo HTMLHelper::_('jgrid.state', $states, $item->state, $i, 'aitools.', true); ?>
                                    </td>
                                    <td>
                                        <a href="<?php echo $item->editLink; ?>"><?php echo $this->escape($item->title); ?></a>
                                        <div class="small"><?php echo $item->description; ?></div>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($item->core): ?>
                                        <span class="badge text-bg-secondary"><?php echo Text::_('COM_ROUTE66_CORE'); ?></span>
                                        <?php else: ?>
                                        <span class="badge text-bg-info"><?php echo Text::_('COM_ROUTE66_CUSTOM'); ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="d-none d-md-table-cell text-center">
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
