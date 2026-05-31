<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Firecoders\Component\Route66\Administrator\Helper\Route66Helper;
use Joomla\CMS\Language\Text;

$wa = $this->document->getWebAssetManager();
$wa->useScript('table.columns')->useScript('multiselect');
$wa->registerAndUseScript('route66.ai.tools.js', 'route66/ai/tools.js', [], ['defer' => true]);

?>

<div class="container-fluid">
    <?php if (empty($this->items)) : ?>
    <div class="alert alert-info">
        <span class="icon-info-circle" aria-hidden="true"></span><span class="visually-hidden"><?php echo Text::_('INFO'); ?></span>
        <?php echo Text::_('JGLOBAL_NO_MATCHING_RESULTS'); ?>
    </div>
    <?php else: ?>
    <div class="row row-cols-1 row-cols-md-3 g-3">
    <?php foreach ($this->items as $item): ?>
        <div class="col">
            <div class="card h-100 border shadow">
                <div class="card-body">
                    <h4 class="card-title"><?php echo htmlspecialchars($item->title); ?></h4>
                    <p class="card-text small"><?php echo htmlspecialchars($item->description); ?></p>
                    <button data-ai-tool-id="<?php echo $item->id; ?>" data-ai-tool-target="<?php echo $item->target; ?>" class="route66-ai-generate-button btn btn-success btn-sm mt-2" title="<?php echo Text::_('COM_ROUTE66_GENERATE_TOOLTIP'); ?>"><?php echo Text::_('COM_ROUTE66_GENERATE'); ?></button>
                    <a class="btn btn-primary btn-sm mt-2" title="<?php echo Text::_('COM_ROUTE66_CUSTOMIZE_TOOLTIP'); ?>" href="index.php?option=com_route66&view=aitool&id=<?php echo $item->id; ?>&tmpl=component&layout=modal"><?php echo Text::_('COM_ROUTE66_CUSTOMIZE'); ?></a>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    <?php endif; ?>
    <?php echo Route66Helper::copyrights(); ?>
</div>
