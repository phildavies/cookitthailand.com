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

use Joomla\CMS\HTML\HTMLHelper;
use function GuzzleHttp\Psr7\normalize_header;

defined('_JEXEC') or die('Restricted Access');

$options = [
    'orderFieldSelector' => '#list_fullordering',
    'limitFieldSelector' => '#list_limit',
    'searchBtnSelector' => '.filter-search-bar__button',
    'filtersHidden' => $filtersHidden
];

HTMLHelper::_('searchtools.form', '#adminForm', $options);
?>
<?php if (!JCH_PRO): ?>
    <script>
        document.querySelector('#toolbar-share button.button-share').disabled = true;
    </script>
<?php endif; ?>
<?php if (version_compare(JVERSION, '3.999.999', 'le')):
    include('navigation.php');
endif; ?>
<!-- Administrator form for browse views -->
<form action="index.php" method="post" name="adminForm" id="adminForm">
    <div id="j-main-container" class="j-main-container">
        <!-- Filters and ordering -->
        <?= $this->fetch('page_cache_filters.php', $data); //j3/4    ?>

        <?php if (!count($items)): ?>
            <!-- No records -->
            <?= $this->fetch('page_cache_norecords.php', $data); //j3/4    ?>
        <?php else: ?>
            <div style="overflow-x:auto">
                <?php $tableClass = (version_compare(JVERSION, '4', 'lt')) ? 'table table-striped table-hover' : 'table table-hover' ?>
                <table class="<?= $tableClass; ?>" id="itemsList">
                    <thead>
                    <!-- Table header -->
                    <?= $this->fetch('page_cache_table_header.php', $data);//j3/4     ?>
                    </thead>
                    <tfoot>
                    <!-- Table footer. The default is showing the pagination footer. -->
                    <?= $this->fetch('page_cache_table_footer.php', $data); ?>
                    </tfoot>
                    <tbody>
                    <!--Table body when records are present -->
                    <?= $this->fetch('page_cache_withrecords.php', $data); ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>

        <!-- Hidden form fields -->
        <div>
            <input type="hidden" name="boxchecked" id="boxchecked" value="0"/>
            <input type="hidden" name="option" id="option" value="com_jchoptimize"/>
            <input type="hidden" name="view" id="view" value="PageCache"/>
            <input type="hidden" name="task" id="task" value=""/>
            <?= HTMLHelper::_('form.token'); ?>
        </div>
    </div>
</form>