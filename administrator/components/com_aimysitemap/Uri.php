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
 defined( '_JEXEC' ) or die(); use Joomla\CMS\Uri\Uri; class AimySitemapURI extends Uri { public function getResource( $entities = false, $encode = true ) { $res = $this->getPath() ?: ''; $res = str_replace( "\r", '', $res ); $res = str_replace( "\n", '', $res ); if ( $entities ) { $res = htmlspecialchars( $res, ENT_COMPAT, 'UTF-8', false ); } else { $res = htmlspecialchars_decode( $res, ENT_COMPAT ); } if ( $q = $this->getQuery() ) { $res .= '?' . ( $entities ? htmlspecialchars( $q, ENT_COMPAT, 'UTF-8', false ) : $q ); } if ( $encode ) { $res = str_replace( ' ', '%20', $res ); $res = str_replace( '+', '%20', $res ); $ua = str_split( $res ); $res = ''; foreach ( $ua as $c ) { $res .= ( ord( $c ) > 0x7f ? urlencode( $c ) : $c ); } } else { $res = urldecode( $res ); } return $res; } public function getResourceHTTP() { return $this->getResource(); } public function getResourceHref() { return $this->getResource( true, true ); } public function getResourceHTML() { return $this->getResource( true, false ); } } 
