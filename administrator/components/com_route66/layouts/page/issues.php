<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

extract($displayData);

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->useScript('joomla.dialog-autocreate');
?>
<?php if (\count($issues)): ?>
<?php foreach ($issues as $issue): ?>
<div class="route66-analyzer-result">
    <span class="route66-analyzer-result-icon me-1 p-1 border rounded-circle <?php echo $issue->type == 'danger' ? 'bad' : 'ok'; ?>"></span>
    <span class="route66-analyzer-result-text"><?php echo $issue->description; ?></span> 
</div>
<?php endforeach; ?>
<?php else: ?>
<div class="route66-analyzer-result">
    <span class="route66-analyzer-result-icon me-1 p-1 border rounded-circle good"></span>
    <span class="route66-analyzer-result-text"><?php echo Text::_('COM_ROUTE66_NO_ISSUES'); ?></span> 
</div>
<?php endif; ?>


