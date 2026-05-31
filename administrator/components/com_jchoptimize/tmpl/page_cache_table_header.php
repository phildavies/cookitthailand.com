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
<!-- Header row -->
<?php if (version_compare(JVERSION, '4', 'lt')): ?>
    <tr>
        <th>
            <input type="checkbox" name="checkall-toggle" class="form-check-input" onclick="Joomla.checkAll(this)"
                   data-bs-toggle="tooltip" title="Select all items">
        </th>
        <th>
            <a href="#" onclick="return false;" class="js-stools-column-order hasPopover"
               data-order="mtime" data-direction="ASC" data-name="Last modified time"
               title="Last modified time" data-content="Click to sort by this column" data-placement="top">
                <?= Text::_('COM_JCHOPTIMIZE_PAGECACHE_MTIME') ?> <?=  $mtimeSelected ?>
            </a>
        </th>
        <th>
            <a href="#" onclick="return false;" class="js-stools-column-order hasPopover"
               data-order="url" data-direction="ASC" data-name="Page URL"
               title="Page URL" data-content="Click to sort by this column" data-placement="top">
                <?= Text::_('COM_JCHOPTIMIZE_PAGECACHE_URL') ?> <?=  $urlSelected ?>
            </a>
        </th>
        <th style="text-align: center;">
            <a href="#" onclick="return false;" class="js-stools-column-order hasPopover"
               data-order="device" data-direction="ASC" data-name="Device"
               title="Device" data-content="Click to sort by this column" data-placement="top">
                <?= Text::_('COM_JCHOPTIMIZE_PAGECACHE_DEVICE') ?> <?=  $deviceSelected ?>
            </a>
        </th>
        <th>
            <a href="#" onclick="return false;" class="js-stools-column-order hasPopover"
               data-order="adapter" data-direction="ASC" data-name="Adapter"
               title="Adapter" data-content="Click to sort by this column" data-placement="top">
                <?= Text::_('COM_JCHOPTIMIZE_PAGECACHE_ADAPTER') ?> <?=  $adapterSelected ?>
            </a>
        </th>
        <th style="text-align: center;">
            <a href="#" onclick="return false;" class="js-stools-column-order hasPopover"
               data-order="http-request" data-direction="ASC" data-name="HTTP Request"
               title="HTTP Request" data-content="Click to sort by this column" data-placement="top">
                <?= Text::_('COM_JCHOPTIMIZE_PAGECACHE_HTTP_REQUEST') ?> <?=  $httpRequestSelected ?>
            </a>
        </th>
        <th class="hidden-phone hidden-tablet d-none d-sm-none d-md-none d-lg-table-cell">
            <a href="#" onclick="return false;" class="js-stools-column-order hasPopover"
               data-order="id" data-direction="ASC" data-name="Cache ID"
               title="Cache ID" data-content="Click to sort by this column" data-placement="top">
                <?= Text::_('COM_JCHOPTIMIZE_PAGECACHE_ID') ?> <?=  $idSelected ?>
            </a>
        </th>
    </tr>
<?php else: ?>
    <tr>
        <th>
            <input type="checkbox" name="checkall-toggle" class="form-check-input" onclick="Joomla.checkAll(this)"
                   data-bs-toggle="tooltip" title="Select all items">
        </th>
        <th>
            <a href="" onclick="return false;"
               class="js-stools-column-order <?= $mtimeSelected[1]; ?> js-stools-button-sort"
               data-order="mtime"
               data-direction="ASC"
               data-caption="" <?= $mtimeSelected[2]; ?>
            >
                <span><?= Text::_('COM_JCHOPTIMIZE_PAGECACHE_MTIME'); ?></span>
                <?= $mtimeSelected[0]; ?>
                <span class="visually-hidden">
Sort Table By:		Last modified time	</span>
            </a>
        </th>
        <th>
            <a href="" onclick="return false;"
               class="js-stools-column-order <?= $urlSelected[1]; ?> js-stools-button-sort"
               data-order="url"
               data-direction="ASC"
               data-caption="" <?= $urlSelected[2]; ?>
            >
                <span><?= Text::_('COM_JCHOPTIMIZE_PAGECACHE_URL'); ?></span>
                <?= $urlSelected[0] ?>
                <span class="visually-hidden">
Sort Table By:		URL	</span>
            </a>
        </th>
        <th style="text-align: center;">
            <a href="" onclick="return false;"
               class="js-stools-column-order <?= $deviceSelected[1]; ?> js-stools-button-sort"
               data-order="device"
               data-direction="ASC"
               data-caption="" <?= $deviceSelected[2]; ?>
            >
                <span><?= Text::_('COM_JCHOPTIMIZE_PAGECACHE_DEVICE'); ?></span>
                <?= $deviceSelected[0]; ?>
                <span class="visually-hidden">
Sort Table By:		Device	</span>
            </a>
        </th>
        <th>
            <a href="" onclick="return false;"
               class="js-stools-column-order <?= $adapterSelected[1]; ?> js-stools-button-sort"
               data-order="adapter"
               data-direction="ASC"
               data-caption="" <?= $adapterSelected[2]; ?>
            >
                <span><?= Text::_('COM_JCHOPTIMIZE_PAGECACHE_ADAPTER'); ?></span>
                <?= $adapterSelected[0]; ?>
                <span class="visually-hidden">
Sort Table By:		URL	</span>
            </a>
        </th>
        <th>
            <a href="" onclick="return false;"
               class="js-stools-column-order <?= $httpRequestSelected[1]; ?> js-stools-button-sort"
               data-order="http-request"
               data-direction="ASC"
               data-caption="" <?= $httpRequestSelected[2]; ?>
            >
                <span><?= Text::_('COM_JCHOPTIMIZE_PAGECACHE_HTTP_REQUEST') ?></span>
                <?= $httpRequestSelected[0]; ?>
                <span class="visually-hidden">
Sort Table By:		URL	</span>
            </a>
        </th>
        <th class="hidden-phone hidden-tablet d-none d-sm-none d-md-none d-lg-none d-xl-none d-xxl-table-cell">
            <a href="" onclick="return false;"
               class="js-stools-column-order <?= $idSelected[1]; ?> js-stools-button-sort"
               data-order="id"
               data-direction="ASC"
               data-caption="" <?= $idSelected[2]; ?>
            >
                <span><?= Text::_('COM_JCHOPTIMIZE_PAGECACHE_ID'); ?></span>
                <?= $idSelected[0]; ?>
                <span class="visually-hidden">Sort Table By: ID</span>
        </th>
    </tr>
<?php endif; ?>
