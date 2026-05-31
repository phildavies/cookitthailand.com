<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Extension\ExtensionHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Folder;
use Joomla\Registry\Registry;

return new class () implements InstallerScriptInterface {
    protected $upgrade = false;

    public function install(InstallerAdapter $adapter): bool
    {
        return true;
    }

    public function update(InstallerAdapter $adapter): bool
    {
        return true;
    }

    public function uninstall(InstallerAdapter $adapter): bool
    {
        return true;
    }

    public function preflight(string $type, InstallerAdapter $adapter): bool
    {
        if (version_compare(JVERSION, '5.3.0', '<')) {
            Factory::getApplication()->enqueueMessage('Route 66 v2 requires Joomla 5.3 or newer', 'error');
            return false;
        }

        if (version_compare(phpversion(), '8.2', '<')) {
            Factory::getApplication()->enqueueMessage('Route 66 v2 requires PHP 8.2 or later', 'error');
            return false;
        }

        if ($type === 'update') {
            $version = $this->getInstalledVersion();

            if (version_compare($version, '1.12.0', '>')) {
                $schema = $this->fixSchemaVersion($version);
            }

            if (version_compare($version, '2.0.0', '<')) {
                $this->upgrade = true;
            }

            if ($this->upgrade) {
                $this->createBackupTable();
            }
        }

        return true;
    }

    public function postflight(string $type, InstallerAdapter $adapter): bool
    {
        $this->removeDeprecatedExtensions($adapter);

        if ($this->upgrade && file_exists(JPATH_SITE.'/plugins/route66/hikashop/hikashop.php')) {
            $this->removeProExtensions($adapter);
        }

        $this->setSiteUrl();

        if ($type === 'install' || $this->upgrade) {
            $this->enablePlugins();
        }

        if ($this->upgrade) {

            $this->upgradeParams();
            $this->removeScriptsFolder();

            $application = Factory::getApplication();
            $application->enqueueMessage('Route 66 has been upgraded. Please <a href="index.php?option=com_route66&view=upgrade">click here to import data from the previous version</a>.', 'warning');
        }

        $this->setSystemPluginOrdering();

        return true;
    }

    protected function setSiteUrl()
    {
        $params = ComponentHelper::getParams('com_route66');

        if ($params->get('site_url')) {
            return;
        }

        $params->set('site_url', Uri::root(false));

        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->update($db->qn('#__extensions'));
        $query->set($db->qn('params') . ' = ' . $db->q($params->toString()));
        $query->where($db->qn('type') . ' = ' . $db->q('component'));
        $query->where($db->qn('element') . ' = ' . $db->q('com_route66'));
        $db->setQuery($query);
        $db->execute();
    }

    protected function removeDeprecatedExtensions(InstallerAdapter $adapter)
    {
        $installer = $adapter->getParent();

        $extension = ExtensionHelper::getExtensionRecord('route66metadata', 'plugin', 0, 'content');
        if ($extension && $extension->type) {
            $installer->uninstall($extension->type, $extension->extension_id);
        }

        $extension = ExtensionHelper::getExtensionRecord('route66seo', 'plugin', 0, 'content');
        if ($extension && $extension->type) {
            $installer->uninstall($extension->type, $extension->extension_id);
        }

        $extension = ExtensionHelper::getExtensionRecord('route66', 'plugin', 0, 'installer');
        if ($extension && $extension->type) {
            $installer->uninstall($extension->type, $extension->extension_id);
        }

        $extension = ExtensionHelper::getExtensionRecord('route66seo', 'plugin', 0, 'k2');
        if ($extension && $extension->type) {
            $installer->uninstall($extension->type, $extension->extension_id);
        }

        $extension = ExtensionHelper::getExtensionRecord('k2', 'plugin', 0, 'route66');
        if ($extension && $extension->type) {
            $installer->uninstall($extension->type, $extension->extension_id);
        }

        $extension = ExtensionHelper::getExtensionRecord('route66pagespeed', 'plugin', 0, 'system');
        if ($extension && $extension->type) {
            $installer->uninstall($extension->type, $extension->extension_id);
        }

        $extension = ExtensionHelper::getExtensionRecord('mod_route66seo', 'module', 1);
        if ($extension && $extension->type) {
            $installer->uninstall($extension->type, $extension->extension_id);
        }
    }

    protected function removeProExtensions(InstallerAdapter $adapter)
    {
        $installer = $adapter->getParent();

        $extension = ExtensionHelper::getExtensionRecord('eshop', 'plugin', 0, 'route66');
        if ($extension && $extension->type) {
            $installer->uninstall($extension->type, $extension->extension_id);
        }

        $extension = ExtensionHelper::getExtensionRecord('virtuemart', 'plugin', 0, 'route66');
        if ($extension && $extension->type) {
            $installer->uninstall($extension->type, $extension->extension_id);
        }

        $extension = ExtensionHelper::getExtensionRecord('hikashop', 'plugin', 0, 'route66');
        if ($extension && $extension->type) {
            $installer->uninstall($extension->type, $extension->extension_id);
        }

        $extension = ExtensionHelper::getExtensionRecord('joomcck', 'plugin', 0, 'route66');
        if ($extension && $extension->type) {
            $installer->uninstall($extension->type, $extension->extension_id);
        }
    }

    protected function removeScriptsFolder()
    {
        if (is_dir(JPATH_SITE.'/media/route66/scripts')) {
            Folder::delete(JPATH_SITE.'/media/route66/scripts');
        }
    }

    protected function enablePlugins()
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->update($db->qn('#__extensions'));
        $query->set($db->qn('enabled') . ' = '.$db->q(1));
        $query->where($db->qn('type') . ' = ' . $db->q('plugin'));
        $query->where('(' . $db->qn('folder') . ' = ' . $db->q('route66') . ' OR ' . $db->qn('element') . ' = ' . $db->q('route66') . ')');
        $db->setQuery($query);
        $db->execute();
    }

    protected function setSystemPluginOrdering()
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('MAX('.$db->qn('ordering').')');
        $query->from($db->qn('#__extensions'));
        $query->where($db->qn('type') . ' = ' . $db->q('plugin'));
        $query->where($db->qn('folder') . ' = ' . $db->q('system'));
        $db->setQuery($query);
        $ordering = (int) $db->loadResult();
        $ordering++;

        $query = $db->getQuery(true);
        $query->update($db->qn('#__extensions'));
        $query->set($db->qn('ordering') . ' = '.$ordering);
        $query->where($db->qn('type') . ' = ' . $db->q('plugin'));
        $query->where($db->qn('folder') . ' = ' . $db->q('system'));
        $query->where($db->qn('element') . ' = ' . $db->q('route66'));
        $db->setQuery($query);
        $db->execute();
    }

    protected function getInstalledVersion()
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->qn('manifest_cache'))->from($db->qn('#__extensions'))->where($db->qn('element') . ' = ' . $db->q('pkg_route66'));
        $db->setQuery($query);
        $result = $db->loadResult();

        $manifest = json_decode($result);
        return $manifest->version;
    }

    protected function upgradeParams()
    {
        $params = ComponentHelper::getParams('com_route66');

        $params->set('sitemap_limit', $params->get('sitemapUrlsLimit'));

        $params->set('images_lazy_load', $params->get('lazyloadImages'));
        $params->set('images_lazy_load_mode', $params->get('lazyloadImagesRestrictionsMode'));
        $params->set('images_lazy_load_classname', $params->get('lazyloadImagesClass'));

        $params->set('iframes_lazy_load', $params->get('lazyloadIframes'));
        $params->set('iframes_lazy_load_mode', $params->get('lazyloadIframesRestrictionsMode'));
        $params->set('iframes_lazy_load_classname', $params->get('lazyloadIframesClass'));

        $params->set('iframe_facades', $params->get('facadeIframes'));
        $params->set('iframe_facades_mode', $params->get('facadeIframesRestrictionsMode'));
        $params->set('iframe_facades_classname', $params->get('facadeIframesClass'));

        $params->set('optimize_css', $params->get('optimizeCss'));

        $params->set('canonical_urls', $params->get('canonical'));
        $params->set('canonical_urls_exclusions', $params->get('exclusions'));

        $extensions = [
            'content'    => ['article', 'category'],
            'tags'       => ['tag'],
            'hikashop'   => ['product', 'category'],
            'virtuemart' => ['product', 'category'],
            'eshop'      => ['product'],
            'joomcck'    => ['record'],
        ];

        foreach ($extensions as $extension => $views) {

            $plugin = PluginHelper::getPlugin('route66', $extension);

            if (!$plugin) {
                continue;
            }

            if (!isset($plugin->params)) {
                continue;
            }

            if (!\is_string($plugin->params)) {
                continue;
            }

            if (!$plugin->params) {
                continue;
            }

            $pluginParams = new Registry($plugin->params);

            foreach ($views as $view) {

                $viewParams = $pluginParams->get($view, []);

                foreach ($viewParams as $language => $pattern) {

                    if (!$pattern) {
                        continue;
                    }

                    $params->set('patterns.com_'.$extension.'_'.$view.'.'.$language, $pattern);
                }
            }
        }


        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->update($db->qn('#__extensions'));
        $query->set($db->qn('params') . ' = ' . $db->q($params->toString()));
        $query->where($db->qn('type') . ' = ' . $db->q('component'));
        $query->where($db->qn('element') . ' = ' . $db->q('com_route66'));
        $db->setQuery($query);
        $db->execute();
    }

    protected function fixSchemaVersion($version)
    {
        $component = ExtensionHelper::getExtensionRecord('com_route66', 'component', 1);

        if (!$component) {
            return;
        }

        $db = Factory::getDbo();

        $tables = $db->getTableList();
        if (!\in_array($db->getPrefix().'route66_ai_tools', $tables)) {
            return;
        }

        $query = $db->getQuery(true);
        $query->update($db->qn('#__schemas'));
        $query->set($db->qn('version_id') . ' = ' . $db->q($version));
        $query->where($db->qn('extension_id') . ' = ' . $db->q($component->extension_id));
        $db->setQuery($query);
        $db->execute();
    }

    protected function createBackupTable()
    {
        $db = Factory::getDbo();

        if (!\in_array(Factory::getApplication()->get('dbprefix') . 'route66_metadata_backup', $db->getTableList())) {
            $query = 'RENAME TABLE `#__route66_metadata` TO `#__route66_metadata_backup`';
            $db->setQuery($query);
            $db->execute();
        } else {
            $query = 'DROP TABLE IF EXISTS `#__route66_metadata`';
            $db->setQuery($query);
            $db->execute();
        }
    }
};
