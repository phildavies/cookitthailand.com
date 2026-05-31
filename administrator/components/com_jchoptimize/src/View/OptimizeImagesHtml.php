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

use JchOptimize\Core\Admin\Icons;
use JchOptimize\Core\Mvc\View;
use JchOptimize\Helper\OptimizeImage;
use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Uri\Uri as JUri;

use const JVERSION;

class OptimizeImagesHtml extends View
{
    public function loadResources(): void
    {
        $document = Factory::getDocument();

        $options = [
            'version' => JCH_VERSION
        ];

        $document->addStyleSheet(JUri::root(true) . '/media/com_jchoptimize/core/css/admin.css', $options);
        $document->addStyleSheet(JUri::root(true) . '/media/com_jchoptimize/css/admin-joomla.css', $options);
        $document->addStyleSheet(JUri::root(true) . '/media/com_jchoptimize/filetree/jquery.filetree.css', $options);
        $document->addStyleSheet(
            JUri::root(true) . '/media/com_jchoptimize/bootstrap/css/bootstrap-grid.css',
            $options
        );

        HTMLHelper::_('jquery.framework');

        $document->addScript(JUri::root(true) . '/media/com_jchoptimize/filetree/jquery.filetree.js', $options);
        $document->addScript(JUri::root(true) . '/media/com_jchoptimize/js/platform-joomla.js', $options);

        $ajax_filetree = Route::_('index.php?option=com_jchoptimize&view=Ajax&task=filetree', false);

        $script = <<<JS
		
jQuery(document).ready( function() {
	jQuery("#file-tree-container").fileTree({
		root: "",
		script: "$ajax_filetree",
		expandSpeed: 100,
		collapseSpeed: 100,
		multiFolder: false
	}, function(file) {});
});
JS;

        $document->addScriptDeclaration($script);

        if (JCH_PRO) {
            /** @psalm-var array{view: string, apiParams: string, icons: Icons} $data */
            $data = $this->getData();
            OptimizeImage::loadResources($data['apiParams']);

            HTMLHelper::_('bootstrap.modal');
        }

        $this->removeData('apiParams');

        $options = [
            'trigger' => 'hover focus',
            'placement' => 'right',
            'html' => true
        ];

        HTMLHelper::_('bootstrap.popover', '.hasPopover', $options);
    }

    public function loadToolBar(): void
    {
        ToolbarHelper::title(Text::_(JCH_PRO ? 'COM_JCHOPTIMIZE_PRO' : 'COM_JCHOPTIMIZE'), 'dashboard');

        if (version_compare(JVERSION, '4.0', '>=')) {
            ToolbarHelper::link(
                Route::_('index.php?option=com_jchoptimize'),
                Text::_('COM_JCHOPTIMIZE_TOOLBAR_LABEL_CONTROLPANEL'),
                'home'
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
