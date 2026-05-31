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

namespace JchOptimize\Controller;

use JchOptimize\Core\Admin\Icons;
use JchOptimize\Core\Cdn;
use JchOptimize\Core\Mvc\Controller;
use JchOptimize\Core\PageCache\CaptureCache;
use JchOptimize\Joomla\Plugin\PluginHelper;
use JchOptimize\Model\Updates;
use JchOptimize\View\ControlPanelHtml;
use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;

use function base64_encode;
use function defined;

use const JCH_PRO;

defined('_JEXEC') or die('Restricted Access');

class ControlPanel extends Controller
{
    /**
     * @var ControlPanelHtml
     */
    private ControlPanelHtml $view;

    /**
     * @var Updates
     */
    private Updates $updatesModel;

    /**
     * @var Icons
     */
    private Icons $icons;

    private Cdn $cdn;

    /**
     * Constructor
     *
     * @param Updates $updatesModel
     * @param ControlPanelHtml $view
     * @param Icons $icons
     * @param Cdn $cdn
     * @param Input|null $input
     * @param AdministratorApplication|null $app
     */
    public function __construct(
        Updates $updatesModel,
        ControlPanelHtml $view,
        Icons $icons,
        Cdn $cdn,
        Input $input = null,
        AdministratorApplication $app = null
    ) {
        $this->updatesModel = $updatesModel;
        $this->view = $view;
        $this->icons = $icons;
        $this->cdn = $cdn;

        parent::__construct($input, $app);
    }

    public function execute(): bool
    {
        $this->manageUpdates();

        if (JCH_PRO) {
            /** @see CaptureCache::updateHtaccess() */
            $this->getContainer()->get(CaptureCache::class)->updateHtaccess();
        }

        $this->cdn->updateHtaccess();

        $this->view->setData([
            'view' => 'ControlPanel',
            'icons' => $this->icons
        ]);

        $this->view->loadResources();
        $this->view->loadToolBar();

        if (!PluginHelper::isEnabled('system', 'jchoptimize')) {
            if (JCH_PRO) {
                $editUrl = Route::_(
                    'index.php?option=com_jchoptimize&view=ModeSwitcher&task=setProduction&return=' . base64_encode(
                        (string)Uri::getInstance()
                    ),
                    false
                );
            } else {
                $editUrl = Route::_('index.php?option=com_plugins&filter[search]=JCH Optimize&filter[folder]=system');
            }
            /** @var AdministratorApplication $app */
            $app = $this->getApplication();
            $app->enqueueMessage(
                Text::sprintf('COM_JCHOPTIMIZE_PLUGIN_NOT_ENABLED', $editUrl),
                'warning'
            );
        }

        echo $this->view->render();

        return true;
    }

    private function manageUpdates(): void
    {
        $this->updatesModel->upgradeLicenseKey();
        $this->updatesModel->refreshUpdateSite();
        $this->updatesModel->removeObsoleteUpdateSites();

        if (JCH_PRO) {
            if ($this->updatesModel->getLicenseKey() == '') {
                if (version_compare(JVERSION, '4.0', 'lt')) {
                    $dlidEditUrl = Route::_('index.php?option=com_config&view=component&component=com_jchoptimize');
                } else {
                    $dlidEditUrl = Route::_(
                        'index.php?option=com_installer&view=updatesites&filter[search]=JCH Optimize&filter[supported]=1'
                    );
                }

                /** @var AdministratorApplication $app */
                $app = $this->getApplication();
                $app->enqueueMessage(
                    Text::sprintf('COM_JCHOPTIMIZE_DOWNLOADID_MISSING', $dlidEditUrl),
                    'warning'
                );
            }
        }
    }
}
