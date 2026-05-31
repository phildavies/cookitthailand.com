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
 defined( '_JEXEC' ) or die(); use Joomla\CMS\MVC\Model\ListModel; class AimySitemapModelLinkCheck extends ListModel { protected $items = null; protected function getListQuery() { $db = $this->getDbo(); $q = $db->getQuery( true ); $q->select( $db->quoteName( array( 'url', 'srcs' ) ) ) ->from( $db->quoteName( '#__aimysitemap_broken_links' ) ) ->order( $db->quoteName( 'url' ) ); return $q; } } 
