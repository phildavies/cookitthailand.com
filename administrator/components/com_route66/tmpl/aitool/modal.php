<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Helper\AIHelper;
use Joomla\CMS\Language\Text;

$wa = $this->document->getWebAssetManager();
$wa->useScript('keepalive')->useScript('form.validate');
$wa->addInlineStyle('body.contentpane {padding: 0;} ');
$wa->registerAndUseScript('route66.ai.tool.js', 'route66/ai/tool.js', [], ['defer' => true]);

$form = AIHelper::getPromptForm($this->item);

?>
<div style="display: grid; grid-template-columns: 35% 65%; height: 100vh">
  <div class="d-flex flex-column vh-100 p-3">
    <div class="border-bottom pb-3">
      <h5><?php echo htmlspecialchars($this->item->title); ?></h5>
      <div class="text-muted small"><?php echo htmlspecialchars($this->item->description); ?></div>
    </div>
    <div class="flex-grow-1 overflow-auto border-bottom pt-3 pb-3">
      <div class="form-vertical">
        <?php echo $form->renderFieldset('route66-prompt-fields'); ?>
      </div>
    </div>
    <div class="pt-3">
      <button class="btn btn-primary w-100" type="button" data-ai-tool-id="<?php echo $this->item->id; ?>" id="route66-ai-generate-button">
          <?php echo Text::_('COM_ROUTE66_GENERATE'); ?>
      </button>
    </div>
  </div>
  <div class="p-3">
    <div class="pb-3">
     <h5><?php echo Text::_('COM_ROUTE66_AI_OUTPUT'); ?></h5>
      <div style="min-height: 40vh; max-height: 80vh;" class="overflow-auto border rounded bg-light-subtle mb-3 p-2" id="route66-ai-tool-output">
      </div>
       <div class="d-flex justify-content-end gap-2">
        <button id="route66-ai-insert-button" data-target="<?php echo $this->item->target; ?>" class="btn btn-success btn-sm route66-ai-action-button" disabled="true"><?php echo Text::_('COM_ROUTE66_INSERT'); ?></button>
        <button id="route66-ai-copy-button" class="btn btn-primary btn-sm route66-ai-action-button" disabled="true"><?php echo Text::_('COM_ROUTE66_COPY'); ?></button>
      </div>
    </div>
  </div>
</div>