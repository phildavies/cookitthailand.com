<?php

/**
 * JCH Optimize - Aggregate and minify external resources for optmized downloads
 *
 * @author    Samuel Marshall <sdmarshall73@gmail.com>
 * @copyright Copyright (c) 2010 Samuel Marshall
 * @license   GNU/GPLv3, See LICENSE file
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

use Composer\Autoload\ClassLoader;
use JchOptimize\ContainerFactory;
use JchOptimize\Core\Admin\Tasks;
use JchOptimize\Model\Cache;
use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Installer\Adapter\ComponentAdapter;
use Joomla\CMS\Installer\InstallerScript;
use Joomla\CMS\Log\Log;
use Joomla\Filesystem\Folder;

// Protect from unauthorized access
defined('_JEXEC') or die();

class Com_JchoptimizeInstallerScript extends InstallerScript
{
    protected $primaryKey = 'extension_id';

    protected $allowDowngrades = true;

    protected $deleteFolders = [
        '/administrator/components/com_jchoptimize/cache',
        '/administrator/components/com_jchoptimize/Controller',
        '/administrator/components/com_jchoptimize/Dispatcher',
        '/administrator/components/com_jchoptimize/Helper',
        '/administrator/components/com_jchoptimize/Model',
        '/administrator/components/com_jchoptimize/Platform',
        '/administrator/components/com_jchoptimize/sql',
        '/administrator/components/com_jchoptimize/Toolbar',
        '/administrator/components/com_jchoptimize/View',
        '/administrator/components/com_jchoptimize/lib/tmpl',
        '/administrator/components/com_jchoptimize/lib/src/Core',
        '/administrator/components/com_jchoptimize/lib/src/Command',
        '/administrator/components/com_jchoptimize/lib/src/Controller',
        '/administrator/components/com_jchoptimize/lib/src/Crawlers',
        '/administrator/components/com_jchoptimize/lib/src/Helper',
        '/administrator/components/com_jchoptimize/lib/src/Joomla',
        '/administrator/components/com_jchoptimize/lib/src/Log',
        '/administrator/components/com_jchoptimize/lib/src/Platform',
        '/administrator/components/com_jchoptimize/lib/src/View',
        '/administrator/components/com_jchoptimize/lib/vendor/illuminate/bus',
        '/administrator/components/com_jchoptimize/lib/vendor/illuminate/container',
        '/administrator/components/com_jchoptimize/lib/vendor/illuminate/events',
        '/administrator/components/com_jchoptimize/lib/vendor/illuminate/filesystem',
        '/administrator/components/com_jchoptimize/lib/vendor/illuminate/pipeline',
        '/administrator/components/com_jchoptimize/lib/vendor/illuminate/support',
        '/administrator/components/com_jchoptimize/lib/vendor/illuminate/view',
        '/administrator/components/com_jchoptimize/lib/vendor/illuminate/contracts/Container',
        '/administrator/components/com_jchoptimize/lib/vendor/illuminate/contracts/Events',
        '/administrator/components/com_jchoptimize/lib/vendor/illuminate/contracts/View',
        '/administrator/components/com_jchoptimize/lib/vendor/joomla/di',
        '/administrator/components/com_jchoptimize/lib/vendor/laminas/laminas-cache-storage-adapter-wincache',
        '/administrator/components/com_jchoptimize/lib/vendor/nicmart/tree/src/Builder',
        '/administrator/components/com_jchoptimize/lib/vendor/nicmart/tree/src/Visitor',
        '/administrator/components/com_jchoptimize/lib/vendor/psr/container',
    ];

    protected $deleteFiles = [
        '/administrator/components/com_jchoptimize/lib/src/Model/ApiParams.php',
        '/administrator/components/com_jchoptimize/lib/src/Model/BulkSettings.php',
        '/administrator/components/com_jchoptimize/lib/src/Model/Cache.php',
        '/administrator/components/com_jchoptimize/lib/src/Model/Configure.php',
        '/administrator/components/com_jchoptimize/lib/src/Model/ModeSwitcher.php',
        '/administrator/components/com_jchoptimize/lib/src/Model/OrderPlugins.php',
        '/administrator/components/com_jchoptimize/lib/src/Model/PageCache.php',
        '/administrator/components/com_jchoptimize/lib/src/Model/ReCache.php',
        '/administrator/components/com_jchoptimize/lib/src/Model/ReCacheCliJ3.php',
        '/administrator/components/com_jchoptimize/lib/src/Model/SaveSettingsTrait.php',
        '/administrator/components/com_jchoptimize/lib/src/Model/TogglePlugins.php',
        '/administrator/components/com_jchoptimize/lib/src/Model/Updates.php',
        '/administrator/components/com_jchoptimize/lib/src/Service/ConfigurationProvider.php',
        '/administrator/components/com_jchoptimize/lib/src/Service/DatabaseProvider.php',
        '/administrator/components/com_jchoptimize/lib/src/Service/LoggerProvider.php',
        '/administrator/components/com_jchoptimize/lib/src/Service/ModeSwitcherProvider.php',
        '/administrator/components/com_jchoptimize/lib/src/Service/MvcProvider.php',
        '/administrator/components/com_jchoptimize/lib/src/Service/ReCacheProvider.php',
        '/administrator/components/com_jchoptimize/lib/src/ContainerFactory.php',
        '/administrator/components/com_jchoptimize/lib/src/ControllerResolver.php',
        '/administrator/components/com_jchoptimize/lib/src/GetApplicationTrait.php',
    ];

    /**
     * Runs after install, update or discover_update
     *
     * @param string $type install, update or discover_update
     * @param ComponentAdapter $parent
     *
     * @return void
     */
    public function postflight(string $type, ComponentAdapter $parent): void
    {
        if ($type == 'uninstall') {
            return;
        }

        @include_once(JPATH_ADMINISTRATOR . '/components/com_jchoptimize/autoload.php');

        if (version_compare(JVERSION, '3.99.99', '<=')) {
            $config_j3 = $parent->getParent()->getPath('source') . '/backend/config_j3.xml';
            $config = JPATH_ADMINISTRATOR . '/components/com_jchoptimize/config.xml';

            File::delete($config);
            if (!File::copy($config_j3, $config)) {
                $msg = "<p>Couldn't copy the config.xml file</p>";
                Log::add($msg, Log::WARNING, 'jerror');
            }

            File::delete(JPATH_ADMINISTRATOR . '/components/com_jchoptimize/config_j3.xml');
            File::delete(JPATH_ADMINISTRATOR . '/components/com_jchoptimize/config_j4.xml');
        }

        if ($type == 'update') {
            $tmpComponentFolder = $parent->getParent()->getPath('source');
            $this->autoloadNewFiles($tmpComponentFolder);
            $this->removeFiles();
            //Update new settings
            $this->updateNewSettings();
        }
    }

    /**
     * Runs on uninstallation
     *
     * @param ComponentAdapter $parent Parent object
     *
     * @return  void
     */
    public function uninstall(ComponentAdapter $parent): void
    {
        // Clean up Htaccess file
        @include_once(JPATH_ADMINISTRATOR . '/components/com_jchoptimize/autoload.php');
        Tasks::cleanHtaccess();
        Folder::delete(JPATH_ROOT . '/images/jch-optimize');
        Folder::delete(JPATH_ROOT . '/jchoptimizecapturecache');
        Folder::delete(JPATH_ROOT . '/.jch');

        $container = ContainerFactory::getContainer();
        $container->get(Cache::class)->cleanCache();
    }

    private function updateNewSettings()
    {
        $extensionIds = $this->getInstances(false);
        $id = (int)$extensionIds[0];
        //Settings to add or update
        $newParams = [];
        //Settings to remove
        $removeParams = [];

        //Update storage adapter
        $adapter = $this->getParam('pro_cache_storage_adapter', $id);
        if ($adapter == 'global') {
            $storageMap = [
                'file' => 'filesystem',
                'redis' => 'redis',
                'apcu' => 'apcu',
                'memcached' => 'memcached',
            ];
            $app = Factory::getApplication();
            $handler = $app->get('cache_handler', 'file');

            $newParams['pro_cache_storage_adapter'] = $storageMap[$handler];

            if ($handler == 'memcached') {
                $newParams['memcached_server_host'] = $app->get('memcached_server_host', '1.7.0.0.1');
                $newParams['memcached_server_port'] = $app->get('memcached_server_port', 11211);
            }

            if ($handler == 'redis') {
                $newParams['redis_server_host'] = $app->get('redis_server_host', '127.0.0.1');
                $newParams['redis_server_port'] = $app->get('redis_server_port', 6379);
                $newParams['redis_server_auth'] = $app->get('redis_server_auth', '');
                $newParams['redis_server_db'] = $app->get('redis_server_db', 0);
            }
        }

        //Update Smart Combine settings
        $smartCombineValues = $this->getParam('pro_smart_combine_values', $id);
        if (!empty($smartCombineValues) && is_array($smartCombineValues)) {
            $newParams['pro_smart_combine_values'] = json_encode($smartCombineValues);
        }

        //Update obsolete settings
        $settingsMap = [
            'pro_remove_unused_js_enable' => 'pro_reduce_unused_js_enable',
            'pro_remove_unused_css' => 'pro_reduce_unused_css',
            'pro_optimize_gfont_enable' => 'pro_optimizeFonts_enable'
        ];

        foreach ($settingsMap as $oldSetting => $newSetting) {
            $setting = $this->getParam($oldSetting, $id);
            if (!is_null($setting)) {
                $newParams[$newSetting] = $setting;
                $removeParams[$oldSetting] = $setting;
            }
        }

        //Update new load WEBP setting
        $loadWebp = $this->getParam('pro_load_webp_images', $id);
        $nextGenImages = $this->getParam('pro_next_gen_images', $id);

        if (is_null($loadWebp) && $nextGenImages) {
            $newParams['pro_load_webp_images'] = '1';
        }

        //Update Exclude JavaScript settings
        $oldJsSettings = [
            'excludeJs_peo',
            'excludeJsComponents_peo',
            'excludeScripts_peo',
            'excludeJs',
            'excludeJsComponents',
            'excludeScripts',
            'dontmoveJs',
            'dontmoveScripts',
        ];

        $updateJsSettings = false;

        foreach ($oldJsSettings as $oldJsSetting) {
            $oldJsSettingValue = $this->getParam($oldJsSetting, $id);

            if ($oldJsSettingValue) {
                if (!isset($oldJsSettingValue[0]['url']) && !isset($oldJsSettingValue[0]['script'])) {
                    $updateJsSettings = true;
                }

                break;
            }
        }

        if ($updateJsSettings) {
            $dontmoveJs = (array)$this->getParam('dontmoveJs', $id);
            $dontmoveScripts = (array)$this->getParam('dontmoveScripts', $id);
            $removeParams['dontmoveJs'] = '1';
            $removeParams['dontmoveScripts'] = '1';

            $excludeJsPeoSettingsMap = [
                'excludeJs_peo' => [
                    'ieo' => 'excludeJs',
                    'valueType' => 'url',
                    'dontmove' => $dontmoveJs
                ],
                'excludeJsComponents_peo' => [
                    'ieo' => 'excludeJsComponents',
                    'valueType' => 'url',
                    'dontmove' => $dontmoveJs
                ],
                'excludeScripts_peo' => [
                    'ieo' => 'excludeScripts',
                    'valueType' => 'script',
                    'dontmove' => $dontmoveScripts
                ],
            ];

            foreach ($excludeJsPeoSettingsMap as $excludeJsPeoSettingName => $settingsMap) {
                $excludeJsPeoSetting = (array)$this->getParam($excludeJsPeoSettingName, $id);
                $removeParams[$excludeJsPeoSettingName] = '1';
                $newExcludeJs_peo = [];
                $i = 0;

                foreach ($excludeJsPeoSetting as $excludeJsPeoSettingValue) {
                    $newExcludeJs_peo[$i][$settingsMap['valueType']] = $excludeJsPeoSettingValue;

                    foreach ($settingsMap['dontmove'] as $dontmoveValue) {
                        if (strpos($excludeJsPeoSettingValue, $dontmoveValue) !== false) {
                            $newExcludeJs_peo[$i]['dontmove'] = 'on';
                        }
                    }
                    $i++;
                }

                $excludeJsIeoSetting = (array)$this->getParam($settingsMap['ieo'], $id);
                $removeParams[$settingsMap['ieo']] = '1';

                foreach ($excludeJsIeoSetting as $excludeJsIeoSettingValue) {
                    $i++;
                    $newExcludeJs_peo[$i][$settingsMap['valueType']] = $excludeJsIeoSettingValue;
                    $newExcludeJs_peo[$i]['ieo'] = 'on';

                    foreach ($settingsMap['dontmove'] as $dontmoveValue) {
                        if (strpos($excludeJsIeoSettingValue, $dontmoveValue) !== false) {
                            $newExcludeJs_peo[$i]['dontmove'] = 'on';
                        }
                    }
                }

                $newParams[$excludeJsPeoSettingName] = $newExcludeJs_peo;
            }
        }

        if (!empty($removeParams)) {
            $this->setParams($removeParams, 'remove', $id);
        }

        if (!empty($newParams)) {
            $this->setParams($newParams, 'edit', $id);
        }
    }


    /**
     * Gets parameter value in the extensions row of the extension table
     *
     * @param string $name The name of the parameter to be retrieved
     * @param int $id The id of the item in the Param Table
     *
     * @return  string  The parameter desired
     *
     * @since   3.6
     */
    public function getParam($name, $id = 0)
    {
        if (!\is_int($id) || $id == 0) {
            // Return false if there is no item given
            return false;
        }

        $params = $this->getItemArray('params', $this->paramTable, $this->primaryKey, $id);

        return $params[$name];
    }

    /**
     * Sets parameter values in the extensions row of the extension table. Note that the
     * this must be called separately for deleting and editing. Note if edit is called as a
     * type then if the param doesn't exist it will be created
     *
     * @param array $paramArray The array of parameters to be added/edited/removed
     * @param string $type The type of change to be made to the param (edit/remove)
     * @param int $id The id of the item in the relevant table
     *
     * @return  bool  True on success
     *
     * @since   3.6
     */
    public function setParams($paramArray = null, $type = 'edit', $id = 0)
    {
        if (!\is_int($id) || $id == 0) {
            // Return false if there is no valid item given
            return false;
        }

        $params = $this->getItemArray('params', $this->paramTable, $this->primaryKey, $id);

        if ($paramArray) {
            foreach ($paramArray as $name => $value) {
                if ($type === 'edit') {
                    // Add or edit the new variable(s) to the existing params
                    if (\is_array($value)) {
                        // Convert an array into a json encoded string
                        $params[(string)$name] = array_values($value);
                    } else {
                        $params[(string)$name] = (string)$value;
                    }
                } elseif ($type === 'remove') {
                    // Unset the parameter from the array
                    unset($params[(string)$name]);
                }
            }
        }

        // Store the combined new and existing values back as a JSON string
        $paramsString = json_encode($params);

        $db = Factory::getDbo();
        $query = $db->getQuery(true)
            ->update($db->quoteName($this->paramTable))
            ->set($db->quoteName('params') . ' = ' . $db->quote($paramsString))
            ->where($db->quoteName($this->primaryKey) . ' = ' . $db->quote($id));

        // Update table
        $db->setQuery($query)->execute();

        return true;
    }

    /**
     * Gets each instance of a module in the #__modules table or extension in the #__extensions table
     *
     * @param bool $isModule True if the extension is a module as this can have multiple instances
     * @param string $extension Name of extension to find instance of
     *
     * @return  array  An array of ID's of the extension
     *
     * @since   3.6
     */
    public function getInstances($isModule, $extension = null)
    {
        $extension = $extension ?? $this->extension;

        $db = Factory::getDbo();
        $query = $db->getQuery(true);

        // Select the item(s) and retrieve the id
        if ($isModule) {
            $query->select($db->quoteName('id'));
            $query->from($db->quoteName('#__modules'))
                ->where($db->quoteName('module') . ' = ' . $db->quote($extension));
        } else {
            $query->select($db->quoteName('extension_id'));
            $query->from($db->quoteName('#__extensions'));
            //Special handling for plugins, we extract the element and folder from the extension name
            $parts = explode('_', $extension, 3);

            if (count($parts) == 3 && $parts[0] == 'plg') {
                $extension = $parts[2];
                $folder = $parts[1];

                $query->where($db->quoteName('folder') . ' = ' . $db->quote($folder));
            }

            $query->where($db->quoteName('element') . ' = ' . $db->quote($extension));
        }

        // Set the query and obtain an array of id's
        return $db->setQuery($query)->loadColumn();
    }

    private function autoloadNewFiles($tmpCompFolder)
    {
        //File changes can occur between versions. Let us dare to reset opcache
        // to avoid issues
        if (function_exists('opcache_reset')) {
            opcache_reset();
        }

        clearstatcache();

        $dir = $tmpCompFolder . '/backend';
        //We may have to individually load classes added in this version; If autoloader runs already
        //they wouldn't be included.

        $autoloaders = ClassLoader::getRegisteredLoaders();

        $classMapPath = $dir . '/vendor/composer/autoload_classmap.php';
        $vendorClassMapPath = $dir . '/lib/vendor/composer/autoload_classmap.php';

        $classMap = include($classMapPath);
        $vendorClassMap = include($vendorClassMapPath);

        foreach ($autoloaders as $autoloader) {
            $autoloader->addClassMap($classMap);
            $autoloader->addClassMap($vendorClassMap);

            break;
        }

        include_once($dir . '/lib/src/class_map.php');
    }
}
