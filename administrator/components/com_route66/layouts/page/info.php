<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
\defined('_JEXEC') or die;

use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Layout\LayoutHelper;

extract($displayData); ?>

<table class="table table-responsive">
    <tbody>
        <tr>
            <th scope="row">URL</th>
            <td><a target="_blank" href="<?php echo $item->url; ?>"><?php echo $item->url; ?></a></td>
        </tr>
        <tr>
            <th scope="row"><?php echo Text::_('JGLOBAL_TITLE'); ?></th>
            <td>
                <?php echo $item->title; ?>
                <!--
                <?php if ($item->title): ?>
                <span class="me-3"><?php echo $item->title; ?></span>
                <?php endif; ?>
                <?php if (!$item->title): ?>
                <span class="badge text-bg-danger"><?php echo Text::_('COM_ROUTE66_LABEL_NO_TITLE'); ?></span>
                <?php elseif ($item->duplicate_title): ?>
                <span class="badge text-bg-warning"><?php echo Text::_('COM_ROUTE66_LABEL_DUPLICATE_TITLE'); ?></span>
                <?php endif; ?>-->
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo Text::_('COM_ROUTE66_DESCRIPTION'); ?></th>
            <td>
                <?php echo $item->description; ?>
                <!--
                <?php if ($item->description): ?>
                <span class="me-3"><?php echo $item->description; ?></span>
                <?php endif; ?>
                <?php if (!$item->description): ?>
                <span class="badge text-bg-danger"><?php echo Text::_('COM_ROUTE66_LABEL_NO_DESCRIPTION'); ?></span>
                <?php elseif ($item->duplicate_description): ?>
                <span class="badge text-bg-warning"><?php echo Text::_('COM_ROUTE66_LABEL_DUPLICATE_DESCRIPTION'); ?></span>
                <?php endif; ?>-->
            </td>
        </tr>
        <tr>
            <th scope="row"><?php echo Text::_('JGRID_HEADING_LANGUAGE'); ?></th>
            <td><?php echo $item->language; ?></td>
        </tr>
        <tr>
            <th scope="row"><?php echo Text::_('JFIELD_METADATA_ROBOTS_LABEL'); ?></th>
            <td><?php echo $item->robots; ?></td>
        </tr>
        <tr>
            <th scope="row"><?php echo Text::_('COM_ROUTE66_CANONICAL_URL'); ?></th>
            <td><?php echo $item->canonical; ?></td>
        </tr>
        <tr>
            <th scope="row"><?php echo Text::_('COM_ROUTE66_PAGE_SIZE'); ?></th>
            <td><?php echo Text::sprintf('COM_ROUTE66_SIZE_KB', round($item->size / 1024)) ; ?></td>
        </tr>
        <tr>
            <th scope="row"><?php echo Text::_('COM_ROUTE66_RESPONSE_TIME'); ?></th>
            <td><?php echo Text::sprintf('COM_ROUTE66_TIME_MS', $item->time) ; ?></td>
        </tr>
        <tr>
            <th scope="row"><?php echo Text::_('COM_ROUTE66_CONTENT_ENCODING'); ?></th>
            <td><?php echo $item->content_encoding; ?></td>
        </tr>
        <tr>
            <th scope="row"><?php echo Text::_('COM_ROUTE66_HTTP_STATUS'); ?></th>
            <td><?php echo $item->http_status; ?> <?php if ($item->redirect_url): ?> <span class="fa fa-icon fa-arrow-right"></span> <a target="_blank" href="<?php echo $item->redirect_url; ?>"><?php echo $item->redirect_url; ?></a><?php endif; ?></td>
        </tr>
        <tr>
            <th scope="row"><?php echo Text::_('COM_ROUTE66_LAST_CRAWLED'); ?></th>
            <td><?php echo HTMLHelper::_('date', $item->crawled, Text::_('DATE_FORMAT_LC2')); ?> <joomla-toolbar-button id="toolbar-fetch" task="page.fetch"><button class="button-fetch ms-2 btn btn-light btn-sm" type="button"><?php echo Text::_('COM_ROUTE66_REFRESH_DATA'); ?></button></joomla-toolbar-button></td>
        </tr>
        <?php if ($item->http_status === 200): ?>
        <tr>
            <th scope="row"><?php echo Text::_('COM_ROUTE66_ISSUES'); ?></th>
            <td><?php echo LayoutHelper::render('page.issues', ['issues' => $issues, 'item' => $item]); ?></td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>