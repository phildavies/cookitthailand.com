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
use JchOptimize\Core\Mvc\Controller;
use JchOptimize\Model\ApiParams;
use JchOptimize\View\OptimizeImagesHtml;
use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Language\Text as JText;
use Joomla\CMS\Router\Route as JRoute;
use Joomla\Input\Input;

use function defined;

defined('_JEXEC') or die('Restricted Access');

class OptimizeImages extends Controller
{
    /**
     * @var OptimizeImagesHtml
     */
    private OptimizeImagesHtml $view;

    /**
     * @var ApiParams
     */
    private ApiParams $model;

    /**
     * @var Icons
     */
    private Icons $icons;

    /**
     * Constructor
     *
     * @param ApiParams $model
     * @param OptimizeImagesHtml $view
     * @param Icons $icons
     * @param Input $input
     * @param AdministratorApplication $app
     */
    public function __construct(
        ApiParams $model,
        OptimizeImagesHtml $view,
        Icons $icons,
        Input $input,
        AdministratorApplication $app
    ) {
        $this->model = $model;
        $this->view = $view;
        $this->icons = $icons;

        parent::__construct($input, $app);
    }

    public function execute(): bool
    {
        /** @var Input $input */
        $input = $this->getInput();
        /** @var AdministratorApplication $app */
        $app = $this->getApplication();

        $status = $input->get('status');

        if (is_null($status)) {
            $this->view->setData([
                'view' => 'OptimizeImages',
                'apiParams' => json_encode($this->model->getCompParams()),
                'icons' => $this->icons
            ]);
            $this->view->loadResources();
            $this->view->loadToolBar();

            echo $this->view->render();
        } else {
            if ($status == 'success') {
                $cnt = $input->getInt('cnt', 0);
                $webp = $input->getInt('webp', 0);

                $app->enqueueMessage(sprintf(JText::_('%1$d images successfully optimized, %2$d WEBPs generated.'), $cnt, $webp));
            } else {
                $msg = $input->getString('msg', '');
                $app->enqueueMessage(
                    JText::_('Image optimization failed with message: "' . urldecode($msg) . '"'),
                    'error'
                );
            }

            $app->redirect(JRoute::_('index.php?option=com_jchoptimize&view=OptimizeImages', false));
        }

        return true;
    }
}
