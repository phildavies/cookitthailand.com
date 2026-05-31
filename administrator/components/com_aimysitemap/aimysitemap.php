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
 defined( '_JEXEC' ) or die(); use Joomla\CMS\Factory; use Joomla\CMS\MVC\Controller\BaseController; use Joomla\CMS\Language\Text; if ( ! Factory::getUser()->authorise( 'core.manage', 'com_aimysitemap' ) ) { return Factory::getApplication()->enqueueMessage( Text::_('JERROR_ALERTNOAUTHOR' ), 'warning' ); } require_once( JPATH_COMPONENT . '/helpers/logger.php' ); $ctrl = BaseController::getInstance( 'aimysitemap' ); $ctrl->execute( Factory::getApplication()->input->get( 'task' ) ); $ctrl->redirect(); 
