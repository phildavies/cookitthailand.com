<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Analyzer;

use Firecoders\Component\Route66\Administrator\Helper\Route66Helper;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\Form;
use Joomla\CMS\Language\Multilanguage;
use Joomla\CMS\Layout\FileLayout;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Uri\Uri;

\defined('_JEXEC') or die;

class Analyzer
{
    protected $item    = null;
    protected $form    = null;
    protected $version = null;

    public function __construct($options = [])
    {
        $this->item    = $options['item'] ?? $this->getItem();
        $this->form    = $options['form'] ?? null;
        $this->version = Route66Helper::version();

        $this->loadLanguage();
        $this->loadOptions();
        $this->registerAssets();
    }

    protected function loadLanguage(): void
    {
        $language = Factory::getLanguage();
        $language->load('com_route66', JPATH_ADMINISTRATOR . '/components/com_route66');
    }

    public function displayInForm(): void
    {
        if (!$this->form) {
            return;
        }

        $this->form->setField(new \SimpleXMLElement('<fieldset name="route66" label="COM_ROUTE66_SEO_BADGE" addfieldprefix="Firecoders\Component\Route66\Administrator\Field"></fieldset>'));
        $this->form->setField(new \SimpleXMLElement('<field type="analyzer" name="route66" formsource="/administrator/components/com_route66/forms/page.xml" />'), '', true, 'route66');
        $this->form->setValue('route66', '', $this->item);

        $this->addAITools();
    }

    public function displayInToolbar(): void
    {
        $form = Form::getInstance('page', JPATH_SITE.'/administrator/components/com_route66/forms/page.xml', ['control' => 'jform[route66]']);
        $form->bind($this->item);

        $layout = new FileLayout('toolbar.analyzer', null, ['component' => 'com_route66']);
        $layout->addIncludePath(JPATH_SITE . '/administrator/components/com_route66/layouts');
        $layout->addIncludePath(JPATH_SITE . '/templates/' . Factory::getApplication()->getTemplate() . '/html/layouts');

        $toolbar = ToolBar::getInstance('toolbar');
        $toolbar->prependButton('Custom', $layout->render(['form' => $form]));

        $this->addAITools();
    }

    protected function addAITools()
    {
        $params = ComponentHelper::getParams('com_route66');

        if (!$params->get('ai_service')) {
            return;
        }

        if ($params->get('ai_service') === 'openai' && !$params->get('openai_api_key')) {
            return;
        }

        if ($params->get('ai_service') === 'anthropic' && !$params->get('anthropic_api_key')) {
            return;
        }

        $layout = new FileLayout('toolbar.ai', null, ['component' => 'com_route66']);
        $layout->addIncludePath(JPATH_SITE . '/administrator/components/com_route66/layouts');
        $layout->addIncludePath(JPATH_SITE . '/templates/' . Factory::getApplication()->getTemplate() . '/html/layouts');
        $toolbar = ToolBar::getInstance('toolbar');
        $toolbar->prependButton('Custom', $layout->render([]));
    }

    protected function loadOptions(): void
    {
        $application  = Factory::getApplication();
        $siteLanguage = ComponentHelper::getParams('com_languages')->get('site', 'en-GB');

        $options = [
            'site'            => Uri::root(false),
            'sitename'        => $application->get('sitename'),
            'sitenameInTitle' => $application->get('sitename_pagetitles'),
            'editor'          => $application->input->getCmd('option') === 'com_route66' ? false : $application->get('editor'),
            'locale'          => str_replace('-', '_', $siteLanguage),
            'language'        => $siteLanguage,
            'multilanguage'   => Multilanguage::isEnabled(),
            'worker'          => Uri::root(false) . 'media/route66/js/yoast/worker.min.js?'.$this->version,
            'fields'          => [],
            'route'           => [],
        ];

        $translation = $this->getTranslation();

        if (!empty($translation)) {
            $options['i18n'] = ['translation' => $translation];
        }

        $options['permalink'] = $this->item->url ?? '';

        $extensionOptions = $application->triggerEvent('onRoute66AnalyzerOptions');

        if (\is_array($extensionOptions) && !empty($extensionOptions)) {
            $options = array_merge($options, $extensionOptions);
        }

        Factory::getDocument()->addScriptOptions('route66', $options);
    }

    protected function registerAssets(): void
    {
        $wa = Factory::getDocument()->getWebAssetManager();

        $wa->registerStyle('route66-analyzer', 'route66/analyzer/app.css');
        $wa->registerStyle('route66-search-preview', 'route66/analyzer/serp.css');
        $wa->registerStyle('route66-toolbar', 'route66/analyzer/toolbar.css');

        $wa->registerScript('route66-yoast', 'route66/yoast/main.min.js', ['importmap' => true]);
        $wa->registerScript('route66-analyzer-results', 'route66/analyzer/results.js', ['importmap' => true]);
        $wa->registerScript('route66-analyzer', 'route66/analyzer/app.js', ['importmap' => true], [], ['route66-yoast', 'route66-analyzer-results', 'editors']);
        $wa->registerScript('route66-fields-cloner', 'route66/analyzer/fields.js', ['importmap' => true]);
        $wa->registerScript('route66-parser', 'https://cdn.jsdelivr.net/npm/htmlparser2@10/+esm', ['importmap' => true]);
        $wa->registerScript('route66-ai', 'route66/ai/app.js', ['importmap' => true], [], ['route66-parser', 'editors']);
        $wa->registerScript('route66-readability', 'route66/readability/readability.js');
    }

    protected function getTranslation(): object
    {
        $language = Factory::getLanguage();
        $locale   = strtolower($language->getTag());

        $path  = JPATH_ADMINISTRATOR . '/components/com_route66/language/analyzer';
        $files = ['wp-plugins-wordpress-seo-stable-' . $locale . '.jed.json'];

        if (strpos($locale, '-')) {
            $parts   = explode('-', $locale);
            $files[] = 'wp-plugins-wordpress-seo-stable-' . $parts[0] . '.jed.json';
        }

        // Fallback
        $files[] = 'wp-plugins-wordpress-seo-stable-en-gb.jed.json';

        foreach ($files as $file) {

            if (is_file($path . '/' . $file)) {

                $buffer      = file_get_contents($path . '/' . $file);
                $translation = json_decode($buffer);

                break;
            }
        }

        return $translation;
    }

    protected function getItem()
    {
        PluginHelper::importPlugin('route66');

        $application  = Factory::getApplication();
        $resourceId   = $application->triggerEvent('onRoute66AnalyzerResourceId');

        if (!\is_string($resourceId) || !$resourceId) {
            return null;
        }

        $metadataModel = $application->bootComponent('com_route66')->getMVCFactory()->createModel('Metadata', 'Administrator', ['ignore_request' => true]);
        $metadataModel->setState('filter.resource_id', $resourceId);
        $metadata = $metadataModel->getItem();
        if (!$metadata) {
            $metadata = (object) [];
        }
        $metadata->resource_id = $resourceId;

        $contentAnalysisModel = $application->bootComponent('com_route66')->getMVCFactory()->createModel('ContentAnalysis', 'Administrator', ['ignore_request' => true]);
        $contentAnalysisModel->setState('filter.resource_id', $resourceId);
        $analysis = $contentAnalysisModel->getItem();
        if (!$analysis) {
            $analysis = (object) [];
        }
        $analysis->resource_id   = $resourceId;

        $item = (object) ['metadata' => $metadata, 'analysis' => $analysis];

        return $item;
    }
}
