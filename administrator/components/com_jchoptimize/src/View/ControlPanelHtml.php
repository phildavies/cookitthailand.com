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

namespace JchOptimize\View;

defined('_JEXEC') or die();

use JchOptimize\Core\Mvc\View;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri as JUri;

use function version_compare;

use const JVERSION;

class ControlPanelHtml extends View
{
    public function loadResources(): void
    {
        $document = Factory::getDocument();

        $options = ['version' => JCH_VERSION];
        $document->addStyleSheet(JUri::root(true) . '/media/com_jchoptimize/core/css/admin.css', $options);
        $document->addStyleSheet(JUri::root(true) . '/media/com_jchoptimize/css/admin-joomla.css', $options);
        $document->addStyleSheet('//netdna.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.css', $options);
        $document->addStyleSheet(
            JUri::root(true) . '/media/com_jchoptimize/bootstrap/css/bootstrap-grid.css',
            $options
        );

        HTMLHelper::_('jquery.framework');
        $document->addScript(JUri::root(true) . '/media/com_jchoptimize/js/platform-joomla.js', $options);
        $document->addScript(JUri::root(true) . '/media/com_jchoptimize/core/js/file_upload.js', $options);

        $javascript = 'let configure_url = \'' . Route::_(
            'index.php?option=com_jchoptimize&view=Configure',
            false,
            Route::TLS_IGNORE,
            true
        ) . '\';';
        $document->addScriptDeclaration($javascript);

        $script = <<<JS

window.addEventListener('DOMContentLoaded', (event) => {
    jchPlatform.getCacheInfo();
})
JS;

        $document->addScriptDeclaration($script);

        $aOptions = [
            'trigger' => 'hover focus',
            'placement' => 'right',
            'html' => true
        ];

        HTMLHelper::_('bootstrap.popover', '.hasPopover', $aOptions);
        HTMLHelper::_('bootstrap.modal');
    }

    public function loadToolBar(): void
    {
        ToolbarHelper::title(Text::_(JCH_PRO ? 'COM_JCHOPTIMIZE_PRO' : 'COM_JCHOPTIMIZE'), 'dashboard');

        if (version_compare(JVERSION, '4.0', '>=')) {
            ToolbarHelper::link(
                Route::_('index.php?option=com_jchoptimize&view=OptimizeImages'),
                Text::_('COM_JCHOPTIMIZE_TOOLBAR_LABEL_OPTIMIZEIMAGE'),
                'images'
            );
            ToolbarHelper::link(
                Route::_('index.php?option=com_jchoptimize&view=PageCache'),
                Text::_('COM_JCHOPTIMIZE_TOOLBAR_LABEL_PAGECACHE'),
                'list'
            );
        }

        ToolbarHelper::preferences('com_jchoptimize');
    }
}
