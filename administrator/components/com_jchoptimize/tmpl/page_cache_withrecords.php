<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/joomla-platform
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2020 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

use Joomla\CMS\Language\Text;

defined('_JEXEC') or die('Restricted Access');

?>
<?php $i = 0; ?>
<?php foreach ($items as $item) : ?>
    <tr>
        <td>
            <input type="checkbox" id="cb<?= $i++; ?>" name="cid[]" value="<?= $item['id']; ?>"
                   onclick="Joomla.isChecked(this.checked)" class="form-check-input">
        </td>
        <td>
            <?= date('l, F d, Y h:i:s A', $item['mtime']); ?> GMT
        </td>
        <td>
            <a title="<?= $item['url'] ?>" href="<?= $item['url']; ?>" class="page-cache-url"
               target="_blank"><?= $item['url']; ?></a>
        </td>
        <td style="text-align: center;">
            <?php if ($item['device'] == 'Desktop'): ?>
                <span class="fa fa-desktop" data-bs-toggle="tooltip"
                      title="<?= $item['device']; ?>"></span>
            <?php else: ?>
                <span class="fa fa-mobile-alt" data-bs-toggle="tooltip"
                      title="<?= $item['device']; ?>"></span>
            <?php endif; ?>
        </td>
        <td>
            <?= $item['adapter']; ?>
        </td>
        <td style="text-align: center;">
            <?php if ($item['http-request'] == 'yes'): ?>
                <span class="fa fa-check-circle" style="color: green;"></span>
            <?php else: ?>
                <span class="fa fa-times-circle" style="color: firebrick;"></span>
            <?php endif; ?>
        </td>
        <td class="hidden-phone hidden-tablet d-none d-sm-none d-md-none d-lg-none d-xl-none d-xxl-table-cell">
            <span class="page-cache-id"><?= $item['id']; ?></span>
        </td>
    </tr>
<?php endforeach; ?>