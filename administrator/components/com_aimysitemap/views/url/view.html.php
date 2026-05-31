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
 defined( '_JEXEC' ) or die(); use Joomla\CMS\MVC\View\HtmlView; use Joomla\CMS\Factory; use Joomla\CMS\Toolbar\ToolbarHelper; use Joomla\CMS\Language\Text; class AimySitemapViewUrl extends HtmlView { protected $item; protected $form; public function display( $tpl = null ) { $this->item = $this->get( 'Item' ); $this->form = $this->get( 'Form' ); $errors = $this->get( 'Errors' ); if ( is_array( $errors ) && count( $errors ) ) { throw new Exception( implode( "\n", $errors ), 500 ); return false; } $this->addToolbar(); parent::display( $tpl ); } protected function addToolbar() { Factory::getApplication()->input->set( 'hidemainmenu', true ); ToolbarHelper::title( Text::_( 'AIMY_SM_EDIT_URL' ), '' ); ToolbarHelper::save( 'url.save' ); if( empty( $this->item->id ) ) { ToolbarHelper::cancel( 'url.cancel' ); } else { ToolbarHelper::cancel( 'url.cancel', 'JTOOLBAR_CLOSE' ); } } } 
