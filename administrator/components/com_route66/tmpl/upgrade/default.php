<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Language\Text;

$wa = $this->getDocument()->getWebAssetManager();
$wa->useScript('core')->useScript('bootstrap.popover');

Text::script('COM_ROUTE66_UPGRADE_COMPLETED');
?>

<div class="px-4 py-5 my-5 text-center">
    <span class="fa-8x mb-4 <?php echo $icon; ?>" aria-hidden="true"></span>
    <h1 class="display-5 fw-bold"><?php echo Text::_('COM_ROUTE66_UPGRADE'); ?></h1>
    
    <div class="col-lg-6 mx-auto" id="before-start-panel">
        <p class="lead mb-4">
            <?php echo Text::_('COM_ROUTE66_UPGRADE_DESC'); ?>
        </p>
        <div class="d-grid gap-2 d-sm-flex justify-content-sm-center">
            <button id="startButton" class="btn btn-primary btn-lg px-4 me-sm-3"><?php echo Text::_('COM_ROUTE66_START'); ?></button>
        </div>
    </div>

    <div class="col-lg-6 mx-auto hidden" id="running-panel">
        <p class="lead mb-4" id="update-title">
            <?php echo Text::_('COM_ROUTE66_UPGRADE_RUNNING'); ?>
        </p>
        <div id="progress" class="progress my-3">
            <div id="progress-bar" class="progress-bar progress-bar-striped progress-bar-animated" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
    </div>

</div>



<script>
const beforeStartPanel = document.querySelector('#before-start-panel');
const runningPanel = document.querySelector('#running-panel');
const button = document.querySelector('#startButton');
const progress = document.querySelector('#progress-bar');

const upgrade = async (id, step = 'metadata') => {

    const url = Joomla.getOptions('system.paths').rootFull + 'administrator/index.php?option=com_route66&task=upgrade.run&format=json';

    const data = new FormData();
    data.append('step', step);
    data.append('id', id);
    data.append(window.Joomla.getOptions('csrf.token'), 1);

    try {

        const response = await fetch(url, {
            method: 'POST',
            body: data,
        });

        if (!response.ok) {
            throw new Error(response.status);
            return;
        }

        const json = await response.json();

        if(!json.success) {
            throw new Error(json.message);
            return;
        }

        if(json.data.completed) {
            Joomla.renderMessages({'success': [Joomla.Text._('COM_ROUTE66_UPGRADE_COMPLETED')]});
            progress.style.width = '100%'; 
            return;
        }

        upgrade(json.data.id, json.data.step);
        
    } catch (error) {
        Joomla.renderMessages({'error': [error.message]});
        beforeStartPanel.classList.remove('hidden');
        runningPanel.classList.add('hidden');
    }
};


button.addEventListener('click', (event) => {
    event.preventDefault();
    
    beforeStartPanel.classList.add('hidden');
    runningPanel.classList.remove('hidden');

    upgrade(0);

    progress.style.width = '30%'; 
    
});
</script>