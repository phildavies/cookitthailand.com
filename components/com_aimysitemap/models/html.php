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
 defined( '_JEXEC' ) or die(); require_once( JPATH_ADMINISTRATOR . '/components/com_aimysitemap/Uri.php' ); use Joomla\CMS\MVC\Model\ListModel; class AimySitemapModelHtml extends ListModel { public function __construct( $config = array() ) { if ( empty( $config[ 'filter_fields' ] ) ) { $config[ 'filter_fields' ] = array( 'url', 'a.url', 'title', 'a.title', 'lang', 'a.lang', 'priority', 'a.priority' ); } parent::__construct( $config ); } protected function populateState( $order = null, $dir = null ) { } public function getItems() { $items = parent::getItems(); if ( ! empty( $items ) ) { foreach ( $items as $item ) { $u = new AimySitemapURI( $item->url ); $item->url = $u->getResourceHTML(); $item->href = $u->getResourceHref(); } } return $items; } protected function getListQuery() { $db = $this->getDbo(); $q = $db->getQuery( true ); return $q->select( $db->quoteName( array( 'a.url', 'a.title', 'a.lang', 'a.priority' ) ) ) ->from( $db->quoteName( '#__aimysitemap' ) . ' AS a' ) ->where( $db->quoteName( 'a.state' ) . ' = ' . '1' . ' AND ' . $db->quoteName( 'a.title' ) . ' > ' . $db->quote( '""' ) ); } } 
