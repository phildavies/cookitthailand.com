<?php
/*
 * Copyright (c) 2017-2025 Aimy Extensions, Netzum Sorglos Software GmbH
 * Copyright (c) 2016-2017 Aimy Extensions, Lingua-Systems Software GmbH
 *
 * https://www.aimy-extensions.com/
 *
 * License: GNU GPLv2, see LICENSE.txt within distribution and/or
 *          https://www.aimy-extensions.com/software-license.html
 */
 defined( '_JEXEC' ) or die(); use Joomla\CMS\Language\Text; use Joomla\CMS\Router\Route; use Joomla\CMS\Uri\Uri; function _render_button( $route, $icon, $i18n, $dis = false, $title = false ) { $href = $dis ? '#' : Route::_( 'index.php?' . $route ); $cls = 'aimy-btn' . ( $dis ? ' aimy-btn-disabled' : '' ); $text = Text::_( $i18n ); $attr = $dis ? 'disabled="disabled" onclick="microalert.alert(this.title);return false;"' : ''; if ( $title === false ) { $title = $text; } else { $title = Text::_( $title ); } echo '<a href="', $href, '" class="', $cls, '" ', $attr, ' title="', htmlspecialchars( $title ), '">', '<span class="icon">', '<i class="aimy-icon-', $icon, ' aimy-icon-hg"></i>', '</span>', '<span class="text">', $text, '</span>', '</a>'; } ?>

<div id="j-main-container" class="j-main-container aimy-main clearfix">

<div class="row-fluid" id="aimy-dashboard-container">
<div class="span7">

<h2><?php
 echo Text::_( 'AIMY_SM_DASHBOARD_MAIN' ); ?></h2>

<div class="aimy-icon-container">
<?php
 _render_button( 'option=com_aimysitemap&amp;view=crawl', 'go', 'AIMY_SM_LINK_CRAWL' ); _render_button( 'option=com_aimysitemap&view=urls', 'list', 'AIMY_SM_LINK_MANAGE' ); ?>
</div>


<h2><?php
 echo Text::_( 'AIMY_SM_DASHBOARD_SETUP' ); ?></h2>

<div class="aimy-icon-container">
<?php
 _render_button( 'option=com_aimysitemap&view=robotstxt', 'edit-file', 'AIMY_SM_LINK_ROBOTSTXT' ); _render_button( 'option=com_config&view=component&component=com_aimysitemap', 'wrench', 'JOPTIONS', ! $this->allow_config, $this->allow_config ? 'JOPTIONS' : 'JLIB_APPLICATION_ERROR_ACCESS_FORBIDDEN' ); ?>
</div>

<h2><?php
 echo Text::_( 'AIMY_SM_DASHBOARD_ADVANCED' ); ?></h2>

<div class="aimy-icon-container">
<?php
 _render_button( 'option=com_aimysitemap&amp;view=notify', 'megaphone', 'AIMY_SM_LINK_NOTIFY' ); _render_button( 'option=com_aimysitemap&amp;view=periodic', 'clock', 'AIMY_SM_LINK_PERIODIC', false , 'AIMY_SM_MSG_FEATURE_PRO_ONLY' ); _render_button( 'option=com_aimysitemap&amp;view=linkcheck', 'warning', 'AIMY_SM_LINK_LINKCHECK', false , 'AIMY_SM_MSG_FEATURE_PRO_ONLY' ); ?>
</div>

</div><!-- /.span7 -->
<div class="span5" id="aimy-right">
    <p>

        <a href="https://www.aimy-extensions.com/joomla/sitemap.html" target="_blank" rel="noopener" class="i"><img src="<?php
 echo Uri::root( true ), '/media/com_aimysitemap/go-pro.jpg'; ?>" width="400" height="469" alt="Aimy Sitemap PRO" /></a>

    </p>
    <p>
        <b>Aimy Sitemap  v39.0</b>
        <br/>
        <br/>
        <a href="https://www.aimy-extensions.com/joomla/sitemap.html" target="_blank" rel="noopener">https://www.aimy-extensions.com/joomla/sitemap.html</a>
    </p>
</div><!-- /.span5 -->
</div><!-- /.row-fluid -->

</div>
<?php
 include( JPATH_ADMINISTRATOR . '/components/com_aimysitemap/helpers/footer.php' ); 
