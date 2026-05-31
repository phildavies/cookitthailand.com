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
 defined( '_JEXEC' ) or die(); require_once( JPATH_COMPONENT . '/helpers/config.php' ); require_once( JPATH_COMPONENT . '/helpers/compat.php' ); require_once( JPATH_COMPONENT . '/helpers/debug.php' ); use Joomla\CMS\MVC\Controller\BaseController; use Joomla\CMS\Language\Text; use Joomla\CMS\Router\Route; use Joomla\CMS\Factory; use Joomla\CMS\Uri\Uri; class AimySitemapController extends BaseController { protected $default_view = 'dashboard'; const MAX_MSG_CACHE_SECONDS = 600; public function display( $cachable = false, $urlparams = false ) { $view = $this->input->get( 'view', $this->default_view ); $layout = $this->input->get( 'layout', 'default' ); $id = $this->input->getInt( 'id' ); if ( $view == 'urls' && $layout == 'edit' && ! $this->checkEditId( 'com_aimysitemap.edit.urls', $id ) ) { $this->setError( Text::sprintf( 'JLIB_APPLICATION_ERROR_UNHELD_ID', $id ) ); $this->setMessage( $this->getError(), 'error' ); $this->setRedirect( Route::_( 'index.php?option=com_aimysitemap&view=urls', false ) ); return false; } $cfg = new AimySitemapConfigHelper(); if ( ! $cfg->get( 'default_priority', 0 ) ) { Factory::getApplication()->enqueueMessage( Text::sprintf( 'AIMY_SM_MSG_NOT_CONFIGURED', Route::_( 'index.php?option=com_config&' . 'view=component&' . 'component=com_aimysitemap' ) ), 'warning' ); } $jdoc = Factory::getDocument(); $mediaUrl = sprintf( '%s/media/com_aimysitemap/', Uri::root( true ) ); $jmajor = AimySitemapCompatHelper::getJoomlaMajorVersion(); $versionCss = sprintf( 'backend-j%d.css?r=39.0', $jmajor >= 4 ? 4 : 3 ); $jdoc->addStylesheet( $mediaUrl . 'backend.css?r=39.0' ); $jdoc->addStylesheet( $mediaUrl . $versionCss ); $jdoc->addStylesheet( $mediaUrl . 'microalert.css?r=39.0' ); $jdoc->addScript( $mediaUrl . 'microalert.js?r=39.0' ); AimySitemapCompatHelper::loadFramework( 'jquery' ); parent::display(); return $this; } } 
