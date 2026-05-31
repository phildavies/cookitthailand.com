<?php
/*
 * Copyright (c) 2022-2025 Aimy Extensions, Netzum Sorglos Software GmbH
 *
 * https://www.aimy-extensions.com/
 *
 * License: GNU GPLv2, see LICENSE.txt within distribution and/or
 *          https://www.aimy-extensions.com/software-license.html
 */
 defined( '_JEXEC' ) or die(); require_once( JPATH_ADMINISTRATOR . '/components/com_aimysitemap/helpers/config.php' ); abstract class AimySitemapExcludePatterns { static private $patterns = false; static private $last_matching_pattern = false; static public function is_excluded( $url ) { if ( ! self::$patterns ) { $cfg = new AimySitemapConfigHelper(); self::$patterns = $cfg->get_splitted( 'crawl_exclude_patterns' ); } foreach ( self::$patterns as $pattern ) { if ( self::match( $pattern, $url ) ) { self::$last_matching_pattern = $pattern; return true; } } return false; } static public function get_last_matching_pattern() { return self::$last_matching_pattern; } static private function match( $pat, $s ) { $pps = explode( '*', $pat ); foreach ( $pps as $i => $p ) { $pps[ $i ] = preg_quote( $p, '/' ); } $re = '/^' . implode( '.*?', $pps ) . '$/'; return ( preg_match( $re, $s ) === 1 ); } } 
