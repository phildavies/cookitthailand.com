<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/joomla-platform
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2020 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Model;

use Exception;
use JchOptimize\Core\Admin\Ajax\Ajax as AdminAjax;
use JchOptimize\Core\Admin\Icons;
use JchOptimize\Core\Admin\Json;
use JchOptimize\Core\Exception\ExceptionInterface;
use JchOptimize\Core\Mvc\Model;
use JchOptimize\Core\PageCache\CaptureCache;
use JchOptimize\Core\Registry;

use function defined;

defined('_JEXEC') or die('Restricted Access');

class Configure extends Model
{
    use SaveSettingsTrait;

    private TogglePlugins $togglePluginsModel;

    public function __construct(Registry $params, TogglePlugins $togglePluginsModel)
    {
        $this->togglePluginsModel = $togglePluginsModel;

        $this->setState($params);

        $this->name = 'configure';
    }

    /**
     * @param $autoSetting
     *
     * @return void
     *
     * @throws ExceptionInterface
     */
    public function applyAutoSettings(string $autoSetting)
    {
        $aAutoParams = Icons::autoSettingsArrayMap();

        $aSelectedSetting = array_column($aAutoParams, $autoSetting);
        /** @psalm-var array<string, string> $aSettingsToApply */
        $aSettingsToApply = array_combine(array_keys($aAutoParams), $aSelectedSetting);

        foreach ($aSettingsToApply as $setting => $value) {
            $this->state->set($setting, $value);
        }

        $this->state->set('combine_files_enable', '1');
        $this->saveSettings();
    }

    /**
     * @throws ExceptionInterface
     */
    public function toggleSetting(?string $setting): bool
    {
        if (is_null($setting)) {
            //@TODO some logging here
            return false;
        }

        if ($setting == 'integrated_page_cache_enable') {
            try {
                if (JCH_PRO) {
                    /** @var ModeSwitcher $modeSwitcher */
                    $modeSwitcher = $this->getContainer()->get(ModeSwitcher::class);
                    $modeSwitcher->togglePageCacheState();
                    /** @see CaptureCache::updateHtaccess() */
                    $this->getContainer()->get(CaptureCache::class)->updateHtaccess();
                } else {
                    $this->togglePluginsModel->togglePageCacheState('jchoptimizepagecache');
                }
                return true;
            } catch (Exception $e) {
                return false;
            }
        }

        $iCurrentSetting = (int)$this->state->get($setting);
        $newSetting = (string)abs($iCurrentSetting - 1);

        if ($setting == 'pro_reduce_unused_css' && $newSetting == '1') {
            $this->state->set('optimizeCssDelivery_enable', '1');
        }

        if ($setting == 'optimizeCssDelivery_enable' && $newSetting == '0') {
            $this->state->set('pro_reduce_unused_css', '0');
        }

        $this->state->set($setting, $newSetting);
        $this->saveSettings();

        return true;
    }
}
