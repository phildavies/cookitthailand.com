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
 defined( '_JEXEC' ) or die(); use Joomla\CMS\Component\ComponentHelper; use Joomla\CMS\Table\Table; class AimySitemapConfigHelper { private $cfg = null; public function __construct( $ext = 'com_aimysitemap' ) { $this->cfg = ComponentHelper::getParams( $ext ); } public function get( $key, $default = null ) { if ( empty( $this->cfg ) ) { return null; } return $this->cfg->get( $key, $default ); } public function get_splitted( $key, $default = null, $delimiter = "\n" ) { $val = $this->get( $key, $default ); $vals = array(); if ( ! empty( $val ) ) { $parts = explode( $delimiter, $val ); foreach ( $parts as $part ) { $v = trim( $part ); if ( ! empty( $v ) ) { $vals[] = $v; } } } return $vals; } public function set( $key, $value ) { $this->cfg->set( $key, $value ); $tbl = Table::getInstance( 'extension' ); $tbl->load( ComponentHelper::getComponent( 'com_aimysitemap' )->id ); $tbl->bind( array( 'params' => $this->cfg->toString() ) ); return $tbl->store(); } static public function get_once( $key, $default = null ) { $cfg = new AimySitemapConfigHelper(); return $cfg->get( $key, $default ); } static public function set_once( $key, $value ) { $cfg = new AimySitemapConfigHelper(); return $cfg->set( $key, $value ); } } 
