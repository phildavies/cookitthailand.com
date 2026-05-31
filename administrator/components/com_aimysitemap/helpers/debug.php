<?php
/*
 * Copyright (c) 2025 Aimy Extensions, Netzum Sorglos Software GmbH
 *
 * https://www.aimy-extensions.com/
 *
 * License: GNU GPLv2, see LICENSE.txt within distribution and/or
 *          https://www.aimy-extensions.com/software-license.html
 */
 defined( '_JEXEC' ) or die( 'Access denied' ); if ( ! defined( 'AIMY_SM_DEBUG' ) ) { if ( defined( 'JDEBUG' ) && JDEBUG ) { define( 'AIMY_SM_DEBUG', true ); } else { require_once( JPATH_ADMINISTRATOR . '/components/com_aimysitemap/helpers/config.php' ); if ( AimySitemapConfigHelper::get_once( 'debug', false ) ) { define( 'AIMY_SM_DEBUG', true ); } } } 
