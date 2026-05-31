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
 defined( '_JEXEC' ) or die(); use Joomla\CMS\Factory; use Joomla\CMS\Object\CMSObject; use Joomla\CMS\Access\Access; abstract class AimySitemapRightsHelper { public static function getRights() { $user = Factory::getUser(); $res = new CMSObject(); $actions = Access::getActionsFromFile( JPATH_COMPONENT_ADMINISTRATOR . '/access.xml' ); if ( ! is_array( $actions ) or empty( $actions ) ) { throw new RuntimeException( 'Failed to load set of actions' ); } foreach ( $actions as $action ) { $res->set( $action->name, $user->authorise( $action->name, 'com_aimysitemap' ) ); } return $res; } } 
