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
 defined( '_JEXEC' ) or die(); require_once( JPATH_COMPONENT . '/helpers/rights.php' ); require_once( JPATH_COMPONENT . '/helpers/config.php' ); require_once( JPATH_COMPONENT . '/helpers/kvstore.php' ); use Joomla\CMS\MVC\View\HtmlView; use Joomla\CMS\Toolbar\ToolbarHelper; use Joomla\CMS\Language\Text; class AimySitemapViewLinkCheck extends HtmlView { protected $allow_config = false; protected $allow_export = false; protected $enabled = true; protected $check_done = false; protected $pagination = null; public function display( $tpl = null ) { $this->pagination = $this->get( 'Pagination' ); $rights = AimySitemapRightsHelper::getRights(); $this->allow_export = $rights->get( 'core.manage' ); $this->allow_config = $rights->get( 'core.admin' ); $this->addToolbar(); return parent::display( $tpl ); } protected function addToolbar() { ToolBarHelper::title( Text::_( 'AIMY_SM_LINKCHECK' ), '' ); if ( $this->allow_config ) { ToolBarHelper::preferences( 'com_aimysitemap' ); } } } 
