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
 defined( '_JEXEC' ) or die(); use Joomla\CMS\Factory; class AimySitemapMessageHelper { private $app = null; static private $queue = array(); public function __construct() { $this->app = Factory::getApplication(); } public function error( $msg ) { return $this->queue( $msg, 'error' ); } public function warning( $msg ) { return $this->queue( $msg, 'warning' ); } public function notice( $msg ) { return $this->queue( $msg, 'notice' ); } public function message( $msg ) { return $this->queue( $msg, 'message' ); } public function queue( $msg, $type = 'message' ) { if ( method_exists( $this->app, 'isClient' ) && $this->app->isClient( 'cli' ) ) { static::$queue[] = ( $type != 'message' ? strtoupper( $type ) . ': ' : '' ) . $msg; return; } return $this->app->enqueueMessage( $msg, $type ); } static public function getAndResetQueue() { $queue = static::$queue; static::$queue = array(); return $queue; } } 
