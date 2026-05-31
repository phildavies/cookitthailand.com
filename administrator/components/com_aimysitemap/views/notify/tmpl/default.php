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
 defined( '_JEXEC' ) or die(); use Joomla\CMS\Router\Route; use Joomla\CMS\Language\Text; use Joomla\CMS\HTML\HTMLHelper; use Joomla\CMS\Session\Session; HTMLHelper::_( 'behavior.core' ); $i18n = array( 'notifying' => Text::_( 'AIMY_SM_NOTIFYING' ), 'dont_close' => Text::_( 'AIMY_SM_MSG_DONT_CLOSE' ) ); if ( $this->allow_notify ) { AimySitemapCompatHelper::addInlineJavascript( 'jQuery(document).ready(function()' . '{' . 'var cfg  = ' . json_encode( $this->ping_cfg ) . ';' . 'var i18n = ' . json_encode( $i18n ) . ';' . ( ! $this->has_sitemap_xml ? 'jQuery( "#toolbar-tree-2 button" )' . '.prop( "disabled", true );' : '' ) . 'Joomla.submitbutton = function( task )' . '{' . 'if ( task == "notify.ping" )' . '{' . 'jQuery( "#toolbar-tree-2 button" )' . '.prop( "disabled", true );' . 'jQuery( "#notify-hint" ).html(' . 'jQuery( "<h2></h2>" ).text( i18n.notifying )' . ')' . '.append(' . 'jQuery( "<p></p>" ).append(' . 'jQuery( "<strong></strong>" )' . '.text( i18n.dont_close )' . ')' . ');' . 'for ( var se in cfg.ses )' . '{' . 'AimySitemapPing(' . 'se,' . '"' . Session::getFormToken() . '",' . '"#ping"' . ');' . '}' . 'var checkDone; checkDone = function()' . '{' . 'if ( jQuery( "div.notify-task" ).length == ' . 'jQuery( "div.notify-task[data-done=\'1\']" ).length )' . '{' . 'jQuery( "#notify-hint > p" ).fadeOut( 1200 );' . '}' . 'else' . '{' . 'window.setTimeout( checkDone, 1000 );' . '}' . '};' . 'checkDone();' . 'return false;' . '}' . '};' . '});' ); } ?>

<div id="j-main-container" class="j-main-container aimy-main clearfix">

<?php if ( $this->allow_notify ) : ?>


<form action="<?php
 echo Route::_( 'index.php?option=com_aimysitemap&view=notify' ); ?>" method="post" name="adminForm" id="adminForm">
        <input type="hidden" name="task" value="" />
        <?php echo HTMLHelper::_( 'form.token' ); ?>
</form>


<div id="notify-hint">
    <h1><?php echo Text::_( 'AIMY_SM_NOTIFY_HINT_HEADING' ); ?></h1>
</div>


<div id="ping" class="row-fluid"></div>

<?php endif; ?>

<div class="note important">
<p>
<?php
 echo Text::sprintf( 'AIMY_SM_GOOGLE_XML_SITEMAP_PING_DISCONTINUATION_NOTE', 'https://www.aimy-extensions.com/news/aimy-sitemap/219-update-for-feature-notify-search-engines.html' ); ?>
</p>
<?php if ( $this->show_sitemap_field_hint ) : ?>
<p>
<?php
 echo Text::sprintf( 'AIMY_SM_ROBOTSTXT_SITEMAP_FIELD_MISSING_HINT', Route::_( 'index.php?option=com_aimysitemap&view=robotstxt' ) ); ?>
</p>
<?php endif; ?>
</div>

<div class="note important">
<p>
<?php
 echo Text::sprintf( 'AIMY_SM_INDEXNOW_BING_YANDEX_NOTE', 'https://www.aimy-extensions.com/joomla/indexnow.html', 'https://www.aimy-extensions.com/how-to-use-xml-sitemap-and-indexnow-together.html' ); ?>
</p>
</div>

</div>

<?php
 include( JPATH_ADMINISTRATOR . '/components/com_aimysitemap/helpers/footer.php' ); 
