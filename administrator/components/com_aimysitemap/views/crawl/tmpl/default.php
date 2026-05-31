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
 defined( '_JEXEC' ) or die(); use Joomla\CMS\HTML\HTMLHelper; use Joomla\CMS\Language\Text; use Joomla\CMS\Session\Session; use Joomla\CMS\Uri\Uri; use Joomla\CMS\Router\Route; try { HTMLHelper::_( version_compare( JVERSION, '3.3.0', 'lt' ) ? 'behavior.framework' : 'behavior.core' ); } catch ( Exception $e ) { } $i18n = array( 'linkcheck' => Text::_( 'AIMY_SM_MSG_DO_LINKCHECK' ), 'crawling' => Text::_( 'AIMY_SM_CRAWLING' ), 'dont_close' => Text::_( 'AIMY_SM_MSG_DONT_CLOSE' ), 'manage' => Text::_( 'AIMY_SM_MSG_DO_MANAGE' ), 'errors' => Text::_( 'AIMY_SM_MSG_ERRORS' ), 'added' => Text::_( 'AIMY_SM_MSG_CRAWL_ADDED' ), 'deleted' => Text::_( 'AIMY_SM_MSG_CRAWL_DELETED' ), 'updated' => Text::_( 'AIMY_SM_MSG_CRAWL_UPDATED' ), 'no_updates' => Text::_( 'AIMY_SM_MSG_NO_UPDATES' ), 'finished' => Text::_( 'AIMY_SM_MSG_CRAWL_FINISHED' ), 'init' => Text::_( 'AIMY_SM_MSG_CRAWL_INIT' ), 'abort_first' => Text::_( 'AIMY_SM_MSG_CRAWL_ABORT_FIRST' ), 'retry' => Text::_( 'AIMY_SM_MSG_CRAWL_RETRY' ), 'userabort' => Text::_( 'AIMY_SM_MSG_CRAWL_USERABORT' ) ); if ( $this->allow_crawl ) { AimySitemapCompatHelper::addInlineJavascript( 'window.crawl_resumable = ' . json_encode( $this->resumable ) . ';' . 'jQuery(document).ready(function()' . '{' . 'if ( ! window.crawl_resumable )' . '{' . 'jQuery( "#resume-hint" ).hide();' . '}' . 'jQuery( "#toolbar-unpublish button" )' . '.prop( "disabled", true );' . 'var startCrawl = function(resume)' . '{' . 'g_aimysitemap_abort = false;' . 'jQuery( "#toolbar-tree-2 button" )' . '.prop( "disabled", true );' . 'jQuery( "#toolbar-unpublish button" )' . '.prop( "disabled", false );' . 'jQuery( "#crawl-hint" ).fadeOut();' . 'jQuery( "#resume-hint" ).fadeOut();' . 'AimySitemapCrawl(' . json_encode( $i18n ) . ',' . '"' . Session::getFormToken() . '",' . '"#crawling",' . 'resume,' . 'function( success, msg )' . '{' . 'if ( ! success ) ' . '{' . 'window.crawl_resumable = true;' . '}' . 'jQuery( "#toolbar-unpublish button" )' . '.prop( "disabled", true );' . 'jQuery( "#toolbar-tree-2 button" )' . '.prop( "disabled", false );' . ( defined( 'AIMY_SM_DEBUG' ) ? 'jQuery( "#dl_btn_cnt" ).fadeIn( "slow" );' : '' ) . '}' . ');' . '};' . 'Joomla.submitbutton = function( task )' . '{' . 'if ( task == "crawl.crawl" )' . '{' . 'if ( window.crawl_resumable )' . '{' . 'microalert.confirm(' . json_encode( Text::_( 'AIMY_SM_ASK_RESUME_CRAWL' ) ) . ',' . json_encode( Text::_( 'JYES' ) ) . ',' . json_encode( Text::_( 'JNO' ) ) . ',' . 'function( doResume )' . '{' . 'startCrawl( doResume );' . '}' . ');' . '}' . 'else' . '{' . 'startCrawl(false);' . '}' . 'return false;' . '}' . 'else if ( task == "crawl.abort" )' . '{' . 'g_aimysitemap_abort = true;' . 'window.crawl_resumable = true;' . 'jQuery( "#resume-hint" ).fadeIn();' . 'jQuery( "#toolbar-unpublish button" )' . '.prop( "disabled", true );' . 'jQuery( "#toolbar-tree-2 button" )' . '.prop( "disabled", false );' . 'return false;' . '}' . '};' . ( defined( 'AIMY_SM_DEBUG' ) && ! is_readable( AimySitemapLogger::get_path() ) ? 'jQuery( "#dl_btn_cnt" ).hide();' : '' ) . '});' ); } ?>

<div id="j-main-container" class="j-main-container aimy-main clearfix">

<?php if ( $this->allow_crawl ) : ?>

<form action="<?php
 echo Route::_( 'index.php?option=com_aimysitemap&view=crawl' ); ?>" method="post" name="adminForm" id="adminForm">
        <input type="hidden" name="task" value="" />
        <?php echo HTMLHelper::_( 'form.token' ); ?>
</form>

<div id="crawl-hint">
    <h1><?php echo Text::_( 'AIMY_SM_CRAWL_HINT_HEADING' ); ?></h1>
    <p><?php echo Text::_( 'AIMY_SM_CRAWL_HINT_TEXT' ); ?></p>
</div>

<div id="crawling"></div>

<div id="resume-hint">
<div class="alert alert-info">
    <?php echo Text::_( 'AIMY_SM_LAST_CRAWL_ABORTED_HINT' ); ?>
</div>
</div>

<?php endif; ?>

<?php if ( defined( 'AIMY_SM_DEBUG' ) ) : ?>
<p id="dl_btn_cnt">
    <a class="btn btn-lg btn-warning"
       href="index.php?option=com_aimysitemap&task=crawl.get_log&<?php
 echo Session::getFormToken(); ?>=1"
       target="_blank"><?php
 echo Text::_( 'AIMY_SM_DOWNLOAD_LOG' ); ?></a>
<?php endif; ?>

</div>

<?php
 include( JPATH_ADMINISTRATOR . '/components/com_aimysitemap/helpers/footer.php' ); 
