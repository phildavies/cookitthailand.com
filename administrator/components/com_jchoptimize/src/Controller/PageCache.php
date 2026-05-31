<?php

/**
 * JCH Optimize - Performs several front-end optimizations for fast downloads
 *
 * @package   jchoptimize/joomla-platform
 * @author    Samuel Marshall <samuel@jch-optimize.net>
 * @copyright Copyright (c) 2022 Samuel Marshall / JCH Optimize
 * @license   GNU/GPLv3, or later. See LICENSE file
 *
 * If LICENSE file missing, see <http://www.gnu.org/licenses/>.
 */

namespace JchOptimize\Controller;

use JchOptimize\Core\Laminas\ArrayPaginator;
use JchOptimize\Core\Mvc\Controller;
use JchOptimize\Joomla\Plugin\PluginHelper;
use JchOptimize\Model\ModeSwitcher;
use JchOptimize\Model\PageCache as PageCacheModel;
use JchOptimize\Model\ReCache;
use JchOptimize\View\PageCacheHtml;
use Joomla\Application\AbstractApplication;
use Joomla\CMS\Application\AdministratorApplication;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Uri\Uri;
use Joomla\Input\Input;

use function base64_encode;
use function defined;

use const JCH_PRO;

defined('_JEXEC') or die('Restricted Access');

class PageCache extends Controller
{
    private PageCacheHtml $view;

    private PageCacheModel $pageCacheModel;

    public function __construct(
        PageCacheModel $pageCacheModel,
        PageCacheHtml $view,
        ?Input $input = null,
        ?AbstractApplication $app = null
    ) {
        $this->pageCacheModel = $pageCacheModel;
        $this->view = $view;

        parent::__construct($input, $app);
    }

    /**
     * @inheritDoc
     */
    public function execute()
    {
        /** @var Input $input */
        $input = $this->getInput();
        /** @var AdministratorApplication $app */
        $app = $this->getApplication();

        if ($input->get('task') == 'remove') {
            $success = $this->pageCacheModel->delete((array)$input->get('cid', []));
        }

        if ($input->get('task') == 'deleteAll') {
            $success = $this->pageCacheModel->deleteAll();
        }

        if (JCH_PRO && $input->get('task') == 'recache') {
            /** @var ReCache $reCacheModel */
            $reCacheModel = $this->getContainer()->get(ReCache::class);
            $redirectUrl = Route::_('index.php?option=com_jchoptimize&view=PageCache', false, 0, true);
            $reCacheModel->reCache($redirectUrl);
        }

        if (isset($success)) {
            if ($success) {
                $message = Text::_('COM_JCHOPTIMIZE_PAGECACHE_DELETED_SUCCESSFULLY');
                $messageType = 'success';
            } else {
                $message = Text::_('COM_JCHOPTIMIZE_PAGECACHE_DELETE_ERROR');
                $messageType = 'error';
            }

            $app->enqueueMessage($message, $messageType);
            $app->redirect(Route::_('index.php?option=com_jchoptimize&view=PageCache', false));
        }

        $integratedPageCache = 'jchoptimizepagecache';

        if (JCH_PRO) {
            /** @var ModeSwitcher $modeSwitcher */
            $modeSwitcher = $this->getContainer()->get(ModeSwitcher::class);
            $integratedPageCache = $modeSwitcher->getIntegratedPageCachePlugin();
        }

        if ($integratedPageCache == 'jchoptimizepagecache') {
            if (!PluginHelper::isEnabled('system', 'jchoptimizepagecache')) {
                if (JCH_PRO === '1') {
                    $editUrl = Route::_(
                        'index.php?option=com_jchoptimize&view=Utility&task=togglepagecache&return=' . base64_encode(
                            (string)Uri::getInstance()
                        ),
                        false
                    );
                } else {
                    $editUrl = Route::_(
                        'index.php?option=com_plugins&filter[search]=JCH Optimize Page Cache&filter[folder]=system'
                    );
                }
                $app->enqueueMessage(
                    Text::sprintf('COM_JCHOPTIMIZE_PAGECACHE_NOT_ENABLED', $editUrl),
                    'warning'
                );
            }
        } elseif (JCH_PRO === '1') {
            /** @var ModeSwitcher $modeSwitcher */
            $modeSwitcher = $this->getContainer()->get(ModeSwitcher::class);
            $app->enqueueMessage(
                Text::sprintf(
                    'COM_JCHOPTIMIZE_INTEGRATED_PAGE_CACHE_NOT_JCHOPTIMIZE',
                    Text::_($modeSwitcher->pageCachePlugins[$integratedPageCache])
                ),
                'info'
            );
        }
        /** @var int $defaultListLimit */
        $defaultListLimit = $app->get('list_limit');

        $paginator = new ArrayPaginator($this->pageCacheModel->getItems());
        $paginator->setCurrentPageNumber((int)$input->get('list_page', '1'))
            ->setItemCountPerPage((int)$this->pageCacheModel->getState()->get('list_limit', $defaultListLimit));

        $this->view->setData([
            'items' => $paginator,
            'view' => 'PageCache',
            'paginator' => $paginator->getPages(),
            'pageLink' => 'index.php?option=com_jchoptimize&view=PageCache',
            'adapter' => $this->pageCacheModel->getAdaptorName(),
            'httpRequest' => $this->pageCacheModel->isCaptureCacheEnabled()
        ]);

        $this->view->renderStatefulElements($this->pageCacheModel->getState());
        $this->view->loadResources();
        $this->view->loadToolBar();

        echo $this->view->render();

        return true;
    }
}
