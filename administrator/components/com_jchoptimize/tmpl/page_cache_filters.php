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

defined('_JEXEC') or die('Restricted Access');

?>
<?php if (version_compare(JVERSION, '4', 'lt')): ?>
    <div class="js-stools clearfix">
        <div class="clearfix">
            <div class="js-stools-container-bar">

                <label for="filter_search" class="element-invisible">
                    Search </label>
                <div class="btn-wrapper input-append">
                    <?= $searchInput ?>
                    <button type="submit" class="btn hasTooltip" title="" aria-label="Search"
                            data-original-title="Search">
                        <span class="icon-search" aria-hidden="true"></span>
                    </button>
                </div>
                <div class="btn-wrapper hidden-phone">
                    <button type="button" class="btn hasTooltip js-stools-btn-filter js-stools-btn-filter" title=""
                            data-original-title="Filter the list items.">
                        Search Tools <span class="caret"></span>
                    </button>
                </div>
                <div class="btn-wrapper">
                    <button type="button" id="filter-search-clear-button" class="btn hasTooltip js-stools-btn-clear"
                            title=""
                            data-original-title="Clear">
                        Clear
                    </button>
                </div>
            </div>
            <div class="js-stools-container-list hidden-phone hidden-tablet shown" style="">
                <div class="ordering-select hidden-phone">
                    <div class="js-stools-field-list">
                        <?= $orderingSelectList ?>
                    </div>
                    <div class="js-stools-field-list">
                        <?= $limitList ?>
                    </div>
                </div>
            </div>
            <div class="pull-right" style="padding: 5px; margin-right:5px;">
                <i>Storage: <span class="badge badge-info"><?= $adapter ?></span></i>
                <i class="ms-1">Http Request:
                    <?php if($httpRequest): ?>
                    <span class="badge badge-success">On</span>
                    <?php else: ?>
                    <span class="badge badge-important">Off</span>
                    <?php endif; ?>
                </i>
            </div>
        </div>
        <!-- Filters div -->
        <div class="js-stools-container-filters clearfix <?= $filterVisible ?>">
            <div class="js-stools-field-filter">
                <?= $time1SelectList ?>
            </div>
            <div class="js-stools-field-filter">
                <?= $time2SelectList ?>
            </div>
            <div class="js-stools-field-filter">
                <?= $deviceSelectList ?>
            </div>
            <div class="js-stools-field-filter">
                <?= $adapterSelectList ?>
            </div>
            <div class="js-stools-field-filter">
                <?= $httpRequestSelectList ?>
            </div>
        </div>
    </div>
<?php else: ?>
    <div class="js-stools" role="search">
        <div class="p-3">
            <i>Storage: <span class="badge bg-primary"> <?= $adapter; ?></span> </i>
            <i class="ms-1">Http Request:
                <?php if ($httpRequest): ?>
                    <span class="badge bg-success">On</span>
                <?php else: ?>
                    <span class="badge bg-danger">Off</span>
                <?php endif; ?>
            </i>
        </div>
        <div class="js-stools-container-bar">
            <div class="btn-toolbar">
                <div class="filter-search-bar btn-group">
                    <div class="input-group">
                        <?= $searchInput; ?>
                        <div role="tooltip" id="filter_search-desc" class="filter-search-bar__description">
                            Search for page cache items using the page URL
                        </div>
                        <span class="filter-search-bar__label visually-hidden">
			<label id="filter_search-lbl" for="filter_search">
Search Tags</label>
		</span>
                        <button type="submit" class="filter-search-bar__button btn btn-primary" aria-label="Search">
                            <span class="filter-search-bar__button-icon icon-search" aria-hidden="true"></span>
                        </button>
                    </div>
                </div>
                <div class="filter-search-actions btn-group">
                    <button type="button" class="filter-search-actions__button btn btn-primary js-stools-btn-filter">
                        Filter Options <span class="icon-angle-down" aria-hidden="true"></span>
                    </button>
                    <button type="button" id="filter-search-clear-button"
                            class="filter-search-actions__button btn btn-primary js-stools-btn-clear">
                        Clear
                    </button>

                </div>
                <div class="ordering-select">
                    <div class="js-stools-field-list">
                        <span class="visually-hidden">
                            <label id="list_fullordering-lbl" for="list_fullordering"> Sort Table By:</label>
                        </span>
                        <?= $orderingSelectList; ?>
                    </div>
                    <div class="js-stools-field-list">
                        <span class="visually-hidden">
                            <label id="list_limit-lbl" for="list_limit"> Select number of items per page.</label>
                        </span>
                        <?= $limitList; ?>
                    </div>
                </div>
            </div>
        </div>
        <!-- Filters div -->
        <div class="js-stools-container-filters clearfix <?= $filterVisible; ?>">
            <div class="js-stools-field-filter">
				<span class="visually-hidden"><label id="filter_published-lbl" for="filter_published">
Time 1</label>
</span>
                <?= $time1SelectList; ?>
            </div>
            <div class="js-stools-field-filter">
				<span class="visually-hidden"><label id="filter_published-lbl" for="filter_published">
Time 2</label>
</span>
                <?= $time2SelectList; ?>
            </div>
            <div class="js-stools-field-filter">
				<span class="visually-hidden"><label id="filter_published-lbl" for="filter_published">
Device</label>
</span>
                <?= $deviceSelectList; ?>
            </div>
            <div class="js-stools-field-filter">
				<span class="visually-hidden"><label id="filter_published-lbl" for="filter_published">
Adapter</label>
</span>
                <?= $adapterSelectList; ?>
            </div>
            <div class="js-stools-field-filter">
                <span class="visually-hidden"><label id="filter_published-lbl" for="filter_published">
HTTP Request
</label>
                </span>
                <?= $httpRequestSelectList; ?>
            </div>

        </div>
    </div>
<?php endif; ?>