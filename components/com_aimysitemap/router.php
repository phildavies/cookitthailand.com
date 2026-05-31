<?php
/*
 * Copyright (c) 2022-2025 Aimy Extensions, Netzum Sorglos Software GmbH
 *
 * https://www.aimy-extensions.com/
 *
 * License: GNU GPLv2, see LICENSE.txt within distribution and/or
 *          https://www.aimy-extensions.com/software-license.html
 */
 defined( '_JEXEC' ) or die(); use Joomla\CMS\Component\Router\RouterView; class AimySitemapRouter extends RouterView { public function build( &$query ) { unset( $query[ 'view' ] ); return array(); } public function preprocess( $query ) { if ( isset( $query[ 'Itemid' ] ) ) { $remove_params = array( 'variant', 'prevent_duplicate_titles', 'filter_by_language', 'container_style', 'exclude_images', 'exclude_documents', 'exclude_multimedia', 'show_credits' ); foreach ( $remove_params as $i => $param ) { if ( isset( $query[ $param ] ) ) { unset( $query[ $param ] ); } } } $query[ 'view' ] = 'html'; return parent::preprocess( $query ); } } 
