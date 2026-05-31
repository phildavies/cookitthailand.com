<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;

extract($displayData);

$user     = Factory::getUser($task->created_by);
$created  = HTMLHelper::_('date', $task->created, Text::_('DATE_FORMAT_LC2'));
$modified = HTMLHelper::_('date', $task->modified, Text::_('DATE_FORMAT_LC2'));
?>

<div id="route66-incomplete-task-card" class="card card-outline-secondary mb-3">
    <div class="card-body">
        <h3 class="card-title"><?php echo Text::_('COM_ROUTE66_INCOMPLETE_CRAWL_TASK'); ?></h3>
        <div class="card-subtitle fs-6 mb-2 text-body-secondary"><?php echo $task->created_by ? Text::sprintf('COM_ROUTE66_INCOMPLETE_CRAWL_TASK_INFO_WEB', $user->name, $created, $modified) : Text::sprintf('COM_ROUTE66_INCOMPLETE_CRAWL_TASK_INFO_CLI', $created, $modified); ?></div>
        <p class="card-text"><?php echo Text::_('COM_ROUTE66_INCOMPLETE_CRAWL_TASK_DETAILS'); ?></p>
        <a id="route66-resume-task-btn" class="btn btn-success btn-sm"><?php echo Text::_('COM_ROUTE66_RESUME_TASK'); ?></a>
        <a id="route66-discard-task-btn" class="btn btn-danger btn-sm"><?php echo Text::_('COM_ROUTE66_DISCARD_TASK'); ?></a>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', (event) => {

    disableToolbarButtons();
  
    document.querySelector('#route66-resume-task-btn').addEventListener('click', (e) => {
        e.preventDefault();
        document.querySelector('#route66-incomplete-task-card').remove();
        Joomla.renderMessages({'info': [Joomla.Text._('COM_ROUTE66_CRAWL_RUNNING')]});
        crawl(<?php echo $task->id; ?>);
    });

    document.querySelector('#route66-discard-task-btn').addEventListener('click', async (e) => {

        e.preventDefault();
        document.querySelector('#route66-incomplete-task-card').remove();

        const url = Joomla.getOptions('system.paths').rootFull + 'administrator/index.php?option=com_route66&task=crawler.discard&format=json';
        const data = new FormData();
        data.append('id', <?php echo $task->id; ?>);
        data.append(window.Joomla.getOptions('csrf.token'), 1);

        try {

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
            
        } catch (error) {
            Joomla.renderMessages({'error': [error.message]});
        } finally {
            enableToolbarButtons();
        }
    });

});
</script>