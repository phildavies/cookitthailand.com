<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Filesystem\Folder;

class Pkg_Route66InstallerScript
{
	public function preflight($type, $parent)
	{
		if ($type == 'update')
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->quoteName('manifest_cache'))->from($db->quoteName('#__extensions'))->where($db->quoteName('name') . ' = ' . $db->quote('com_route66'));
			$db->setQuery($query);
			$manifest = json_decode($db->loadResult());
			$installedVersion = $manifest->version;

			if (version_compare($installedVersion, '1.7.1', 'lt'))
			{
				$folder = JPATH_SITE . '/media/route66/scripts';

				if (Folder::exists($folder))
				{
					Folder::delete($folder);
					$cache = Factory::getCache('plg_system_route66_pagespeed', 'output');
					$cache->clean();
				}
			}
		}
	}

	public function postflight($type, $parent)
	{
		// Publish plugins on installation
		if ($type == 'install')
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->update($db->qn('#__extensions'));
			$query->set($db->qn('enabled') . ' = 1');
			$query->where($db->qn('type') . ' = ' . $db->q('plugin'));
			$query->where('(' . $db->qn('folder') . ' = ' . $db->q('route66') . ' OR ' . $db->qn('element') . ' = ' . $db->q('route66pagespeed') . ' OR ' . $db->qn('element') . ' = ' . $db->q('route66seo') . ' OR ' . $db->qn('element') . ' = ' . $db->q('route66metadata') . ' OR ' . $db->qn('element') . ' = ' . $db->q('route66') . '  OR ' . $db->qn('element') . ' = ' . $db->q('route66indexnow') . ')');
			$db->setQuery($query);
			$db->execute();
		}
		elseif ($type == 'update')
		{
			$db = Factory::getDbo();
			$query = $db->getQuery(true);
			$query->select($db->qn('params'));
			$query->from($db->qn('#__extensions'));
			$query->where($db->qn('folder') . ' = ' . $db->q('system'));
			$query->where($db->qn('element') . ' = ' . $db->q('route66'));
			$db->setQuery($query);
			$pluginParams = json_decode($db->loadResult());
			$componentParams = ComponentHelper::getParams('com_route66');
			$updated = false;

			if (isset($pluginParams->canonical) && $componentParams->get('canonical') === null)
			{
				$componentParams->set('canonical', $pluginParams->canonical);
				$updated = true;
			}

			if (isset($pluginParams->redirect) && $componentParams->get('redirect') === null)
			{
				$componentParams->set('redirect', $pluginParams->redirect);
				$updated = true;
			}

			if ($componentParams->get('downloadId') === null)
			{
				$query = $db->getQuery(true);
				$query->select($db->qn('params'));
				$query->from($db->qn('#__extensions'));
				$query->where($db->qn('folder') . ' = ' . $db->q('installer'));
				$query->where($db->qn('element') . ' = ' . $db->q('route66'));
				$db->setQuery($query);
				$pluginParams = json_decode($db->loadResult());

				if (isset($pluginParams->downloadId))
				{
					$componentParams->set('downloadId', $pluginParams->downloadId);
					$updated = true;
				}
			}

			if ($updated)
			{
				$query = $db->getQuery(true);
				$query->update($db->qn('#__extensions'));
				$query->set($db->qn('params') . ' = ' . $db->q($componentParams->toString()));
				$query->where($db->qn('type') . ' = ' . $db->q('component'));
				$query->where($db->qn('element') . ' = ' . $db->q('com_route66'));
				$db->setQuery($query);
				$db->execute();
			}

			$query = $db->getQuery(true);
			$query->update($db->qn('#__modules'));
			$query->set($db->qn('position') . ' = ' . $db->q('submenu'));
			$query->set($db->qn('published') . ' = 1');
			$query->where($db->qn('module') . ' = ' . $db->q('mod_route66seo'));
			$query->where($db->qn('client_id') . ' = 1');
			$db->setQuery($query);
			$db->execute();
		}
	}
}
