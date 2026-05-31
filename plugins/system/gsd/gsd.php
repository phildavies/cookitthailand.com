<?php

/**
 * @package         Google Structured Data
 * @version         6.2.0 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2026 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Layout\LayoutHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use GSD\Schemas\SchemaManager;
use GSD\Helper;


class plgSystemGSD extends CMSPlugin
{
	/**
	 *  Auto loads the plugin language file
	 *
	 *  @var  boolean
	 */
	protected $autoloadLanguage = true;

	/**
	 *  The loaded indicator of helper
	 *
	 *  @var  boolean
	 */
	protected $init;

	/**
	 *  Application Object
	 *
	 *  @var  object
	 */
	protected $app;

	

	/**
	 * Detect Markdown requests and rewrite suffix URLs before routing.
	 *
	 * @return void
	 */
	public function onAfterInitialise()
	{
		if (!$this->app->isClient('site'))
		{
			return;
		}

		if (!$this->setup())
		{
			return;
		}

		if (!$this->params->get('markdown_enabled', false))
		{
			return;
		}

		
	}

	

	/**
	 *  onBeforeCompileHead event to add JSON markup to the document
	 *
	 *  @return void
	 */
	public function onBeforeCompileHead()
	{	
		// Load Helper
		if (!$this->getHelper())
		{
			return;
		}

		if (!$this->params->get('wait_page_render', false) && $markup = $this->getMarkup())
		{
			Factory::getDocument()->addCustomTag($markup);
		}

		// Add Snippets Control
		$this->addRobotSnippetControl();
	}

	/**
	 *  This event is triggered after the framework has rendered the application.
	 *
	 *  @return void
	 */
	public function onAfterRender()
	{
		// Load Helper
		if (!$this->getHelper())
		{
			return;
		}

		

		if ($this->params->get('wait_page_render', false) && $markup = $this->getMarkup())
		{
			$buffer = $this->app->getBody();

			// If </body> exists prepend the markup
			if (strpos($buffer, '</body>'))
			{
				$buffer = str_replace('</body>', $markup . '</body>', $buffer);
			} else 
			// If </body> is not found append markup to document's end
			{
				$buffer .= $markup;
			}
			
			$this->app->setBody($buffer);
		}

		

		// Output log messages if debug is enabled
    	if ($this->params->get('debug', false) && Factory::getUser()->authorise('core.admin'))
    	{
			echo LayoutHelper::render('debug', ['logs' => Helper::$log], JPATH_ADMINISTRATOR . '/components/com_gsd/layouts');
    	}
	}

	/**
	 *  Adds Google Structured Markup to the document in JSON Format
	 *
	 *  @return void
	 */
	private function getMarkup()
	{
		Helper::log($this->app->input->getArray());

		$schemaManager = SchemaManager::getInstance()
			->addSchema(GSD\KnowledgeGraph::get())
			->addSchema($this->getCustomCode())
			->addSchema($this->getJSONBreadcrumbs());

		// Prevent the “WebAssetManager is locked, you came late” error when
		// parsing shortcodes with asset dependencies during onAfterRender.
		try
		{
			Helper::event('onGSDBeforeRender');

		} catch (\Joomla\CMS\WebAsset\Exception\InvalidActionException $th)
		{
			if ($this->params->get('debug'))
			{
				Helper::log($th->getMessage());
			}
		}

		return $schemaManager->render();
	}

	/**
	 *  Route default form's prepare event to onGSDPluginForm to help our plugins manipulate the form
	 *
	 *  @param   Form  $form  The form to be altered.
	 *  @param   mixed  $data  The associated data for the form.
	 *
	 *  @return  boolean
	 */
	public function onContentPrepareForm($form, $data)
	{
		// Run only on backend
		if (!$this->app->isClient('administrator') || !$form instanceof Form)
		{
			return;
		}

		// Load libraries
		if (!$this->setup())
		{
			return;
		}

		Helper::event('onGSDPluginForm', array($form, $data));
	}

	/**
	 * Add Robots Snippet Control
	 * https://webmasters.googleblog.com/2019/09/more-controls-on-search.html
	 *
	 * @return void
	 */
	private function addRobotSnippetControl()
	{
		$robots = Factory::getDocument()->getMetaData('robots');
		
		// Skip, if the existing value contains any of the following text
		if (\NRFramework\Functions::strpos_arr(['noindex', 'nosnippet', 'max-'], $robots))
		{
			return;
		}

		$value = 'max-snippet:-1, max-image-preview:large, max-video-preview:-1';
		$robots = empty($robots) ? $value : $robots . ', ' . $value;

		Factory::getDocument()->setMetaData('robots', $robots);
	}

	

	/**
	 *  Returns Breadcrumbs structured data markup
	 *  https://developers.google.com/structured-data/breadcrumbs
	 *
	 *  @return  string
	 */
	private function getJSONBreadcrumbs()
	{
		if (!$this->params->get('breadcrumbs_enabled', true))
		{
			return;
		}

		$include_home = $this->params->get('include_home', true);
		$home_text    = $this->params->get('breadcrumbs_home', Text::_('GSD_BREADCRUMBS_HOME'));

		// The Joomla's 5 core breadcrumbs module injects its own structured data which conflicts with our plugin.
		// We need to remove it to avoid duplication.
		if ($this->params->get('remove_joomla_breadcrumb_schema') && !$this->params->get('wait_page_render', false))
		{
			$wa = $this->app->getDocument()->getWebAssetManager();
	
			$bcAssetName = 'inline.mod_breadcrumbs-schemaorg';
	
			if ($wa->assetExists('script', $bcAssetName))
			{
				$wa->disableAsset('script', $bcAssetName);
			}
		}

		// Generate JSON
		$json = new \GSD\Json();
		return $json->setData(array(
			'contentType' => 'breadcrumbs',
			'crumbs'      => Helper::getCrumbs($home_text, $include_home)
		))->generate();
	}
	
	/**
	 *  Returns Custom Code
	 *
	 *  @return  string  The Custom Code
	 */
	private function getCustomCode()
	{
		return trim((string) $this->params->get('customcode'));
	}

	/**
	 *  Load required classes and configuration
	 *
	 *  @return  bool 
	 */
	private function setup()
	{
		// Initialize framework
		if (!@include_once(JPATH_PLUGINS . '/system/nrframework/autoload.php'))
		{
			return;
		}

		// Make sure the component is installed and enabled.
		if (!\NRFramework\Extension::componentIsEnabled('gsd'))
		{
			return;
		}

        // Initialize extension library
        if (!@include_once(JPATH_ADMINISTRATOR . '/components/com_gsd/autoload.php'))
        {
            return;
		}

		@include_once(__DIR__ . '/vendor/autoload.php');

		// Load configuration options
		$this->params = Helper::getParams();

		return true;
	}

	/**
	 *  Loads Helper files
	 *
	 *  @return  boolean
	 */
	private function getHelper()
	{
		// Return if is helper is already loaded
		if ($this->init)
		{
			return true;
		}

		// Return if we are not in frontend
		if (!$this->app->isClient('site'))
		{
			return false;
		}

		// Only on HTML documents
		if (Factory::getDocument()->getType() !== 'html')
		{
			return false;
		}

		// Load libraries
		if (!$this->setup())
		{
			return;
		}

		// Return if current page is an XML page
		if (NRFramework\Functions::isFeed() || $this->app->input->getInt('print', 0))
		{
			return false;
		}

		return ($this->init = true);
	}
}
