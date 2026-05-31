<?php
/*
 * Copyright (c) 2017-2025 Aimy Extensions, Netzum Sorglos Software GmbH
 * Copyright (c) 2014-2017 Aimy Extensions, Lingua-Systems Software GmbH
 *
 * https://www.aimy-extensions.com/
 *
 * License: GNU GPLv2, see LICENSE.txt within distribution and/or
 *          https://www.aimy-extensions.com/software-license.html
 */
 defined( '_JEXEC' ) or die(); use Joomla\CMS\Uri\Uri; use Joomla\CMS\Router\Route; use Joomla\CMS\Language\Text; $btns = array( 'dashboard' => array( 'route' => 'option=com_aimysitemap', 'text' => 'AIMY_SM_LINK_DASHBOARD', 'icon' => 'book' ), 'crawl' => array( 'route' => 'option=com_aimysitemap&amp;view=crawl', 'text' => 'AIMY_SM_LINK_CRAWL', 'icon' => 'go' ), 'manage' => array( 'route' => 'option=com_aimysitemap&view=urls', 'text' => 'AIMY_SM_LINK_MANAGE', 'icon' => 'list' ), 'notify' => array( 'route' => 'option=com_aimysitemap&amp;view=notify', 'text' => 'AIMY_SM_LINK_NOTIFY', 'icon' => 'megaphone' ), 'robotstxt' => array( 'route' => 'option=com_aimysitemap&view=robotstxt', 'text' => 'AIMY_SM_LINK_ROBOTSTXT', 'icon' => 'edit-file' ), 'periodic' => array( 'route' => 'option=com_aimysitemap&amp;view=periodic', 'text' => 'AIMY_SM_LINK_PERIODIC', 'icon' => 'clock' ), 'linkcheck' => array( 'route' => 'option=com_aimysitemap&amp;view=linkcheck', 'text' => 'AIMY_SM_LINK_LINKCHECK', 'icon' => 'warning' ), 'options' => array( 'route' => 'option=com_config&view=component&component=com_aimysitemap', 'text' => 'JOPTIONS', 'icon' => 'wrench' ) ); ?>

<div class="clearfix row-fluid" id="aimy-footer">
    <div class="span4">
        <a href="https://www.aimy-extensions.com/"><img src="<?php
 echo Uri::root() . '/media/com_aimysitemap/aimy-logo_100x50.png'; ?>" alt="Aimy Extensions Logo" width="100" height="50" class="logo-lm"/><img
            src="<?php
 echo Uri::root() . '/media/com_aimysitemap/aimy-logo_dm_100x50.png'; ?>" alt="Aimy Extensions Logo" width="100" height="50" class="logo-dm" /></a>
        <br/>
        Aimy Sitemap  Version 39.0
        <br/>
        <a href="https://www.aimy-extensions.com/joomla/sitemap.html" target="_blank" rel="noopener">https://www.aimy-extensions.com/joomla/sitemap.html</a>
    </div>
    <div class="span4">
        <p>
            <?php
 Text::printf( 'AIMY_SM_FOOTER_RATE_ON_JED', 'https://extensions.joomla.org/extensions/extension/' . 'structure-a-navigation/site-map/aimy-sitemap' ); ?>
        </p>
    </div>
    <div class="span4">
        <p>
            <?php
 Text::printf( 'AIMY_SM_FOOTER_NEED_HELP', 'https://www.aimy-extensions.com/joomla/sitemap.html#tab-faq', 'https://www.aimy-extensions.com/joomla/sitemap.html#user-manual', 'https://aimy-extensions.com/images/products/sitemap/com-aimy-sitemap.pdf?r=39.0' ); ?>
        </p>
    </div>
    <div class="clear span12" id="aimy-footer-quicklinks">
    <?php foreach ( $btns as $name => $btn ) : ?>
        <a href="<?php
 echo Route::_( 'index.php?' . $btn[ 'route' ] ); ?>" class="btn btn-secondary"><?php
 if ( isset( $btn[ 'icon' ] ) ) { echo '<i class="aimy-icon-', $btn[ 'icon' ], '"></i>&nbsp;'; } echo Text::_( $btn[ 'text' ] ); ?></a>
    <?php endforeach; ?>
    </div>

</div><!-- /.row -->

<?php
 
