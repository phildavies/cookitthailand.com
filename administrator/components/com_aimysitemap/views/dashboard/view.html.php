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
 defined( '_JEXEC' ) or die(); require_once( JPATH_COMPONENT . '/helpers/rights.php' ); use Joomla\CMS\Factory; use Joomla\CMS\MVC\View\HtmlView; use Joomla\CMS\Toolbar\ToolbarHelper; use Joomla\CMS\Language\Text; class AimySitemapViewDashboard extends HtmlView { protected $allow_config = false; public function display( $tpl = null ) { $rights = AimySitemapRightsHelper::getRights(); $this->allow_config = $rights->get( 'core.admin' ); $this->addToolbar(); parent::display( $tpl ); } protected function addToolbar() { ToolBarHelper::title( Text::_( 'AIMY_SM_DASHBOARD' ), '' ); if ( $this->allow_config ) { ToolBarHelper::preferences( 'com_aimysitemap' ); } } } 
