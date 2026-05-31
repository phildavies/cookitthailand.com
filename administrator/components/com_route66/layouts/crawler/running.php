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
        <h3 class="card-title"><?php echo Text::_('COM_ROUTE66_CRAWL_TASK_RUNNING'); ?></h3>
        <div class="card-subtitle fs-6 text-body-secondary"><?php echo $task->created_by ? Text::sprintf('COM_ROUTE66_INCOMPLETE_CRAWL_TASK_INFO_WEB', $user->name, $created, $modified) : Text::sprintf('COM_ROUTE66_INCOMPLETE_CRAWL_TASK_INFO_CLI', $created, $modified); ?></div>
    </div>
</div>

<script>
window.addEventListener('DOMContentLoaded', (event) => {
    disableToolbarButtons();
});
</script>