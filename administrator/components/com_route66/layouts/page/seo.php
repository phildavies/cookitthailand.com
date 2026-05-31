<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
\defined('_JEXEC') or die;

extract($displayData); ?>

<?php echo $form->renderField('id', 'analysis'); ?>
<?php echo $form->renderField('resource_id', 'analysis'); ?>
<?php echo $form->renderField('link_hash', 'metadata'); ?>
<?php echo $form->renderField('seo_keyphrase', 'analysis'); ?>
<?php echo $form->renderField('seo_score', 'analysis'); ?>

<div id="route66-analyzer-seo-results"></div>

<template id="route66-analyzer-result-template">
    <div class="route66-analyzer-result">
        <span class="route66-analyzer-result-icon me-1 p-1 border rounded-circle"></span>
        <span class="route66-analyzer-result-text text-secondary-emphasis"></span> 
    </div>
</template>