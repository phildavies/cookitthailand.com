<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Helper;

use Firecoders\Component\Route66\Administrator\AI\AIServiceFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormFactoryInterface;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class AIHelper
{
    public static function run(int $id, array $input)
    {
        $ai = AiServiceFactory::create();

        if (!$ai) {
            return '';
        }

        $tool = self::getTool($id);

        if (!$tool) {
            return;
        }

        if (!$tool->prompt) {
            return;
        }

        $options = [
            'instructions' => $tool->instructions,
            'temperature'  => $tool->temperature ?? 0.7,
        ];

        $prompt = self::preparePrompt($tool->prompt, $input);
        $output = $ai->generate($prompt, $options);

        return $output;
    }

    protected static function getTool($id)
    {
        $application = Factory::getApplication();
        $model       = $application->bootComponent('com_route66')->getMVCFactory()->createModel('AITool', 'Administrator', ['ignore_request' => true]);
        $item        = $model->getItem($id);

        return $item;
    }


    protected static function preparePrompt(string $prompt, array $input): string
    {
        $application = Factory::getApplication();

        $defaults = ['keyphrase' => '', 'text' => '', 'title' => '', 'language' => '', 'tone' => ''];
        $values   = array_merge($defaults, $input);

        foreach ($values as $key => $value) {
            $prompt = str_replace('{' . $key . '}', $value, $prompt);
        }

        return $prompt;
    }

    public static function getPromptForm($prompt)
    {
        $form = Factory::getContainer()->get(FormFactoryInterface::class)->createForm('route66-ai-prompt-form', []);

        $form->load('<form addfieldprefix="Firecoders\Component\Route66\Administrator\Field"><fieldset name="route66-prompt-fields"></fieldset></form>');

        if (strpos($prompt->prompt, '{title}') !== false) {
            $form->setField(new \SimpleXMLElement('<field type="text" labelclass="form-label small" class="route66-ai-input form-control-sm" label="COM_ROUTE66_PROMPT_INPUT_TITLE_LABEL" name="title" />'), null, true, 'route66-prompt-fields');
        }

        if (strpos($prompt->prompt, '{text}') !== false) {
            $form->setField(new \SimpleXMLElement('<field type="textarea" labelclass="form-label small" class="route66-ai-input form-control-sm" label="COM_ROUTE66_PROMPT_INPUT_TEXT_LABEL" name="text" />'), null, true, 'route66-prompt-fields');
        }

        if (strpos($prompt->prompt, '{keyphrase}') !== false) {
            $form->setField(new \SimpleXMLElement('<field type="text" labelclass="form-label small" class="route66-ai-input form-control-sm" label="COM_ROUTE66_PROMPT_INPUT_KEYPHRASE_LABEL" name="keyphrase" />'), null, true, 'route66-prompt-fields');
        }

        if (strpos($prompt->prompt, '{language}') !== false) {
            $form->setField(new \SimpleXMLElement('<field type="contentlanguage" labelclass="form-label small" class="route66-ai-input form-select-sm" label="COM_ROUTE66_PROMPT_INPUT_LANGUAGE_LABEL" name="language" />'), null, true, 'route66-prompt-fields');
        }

        if (strpos($prompt->prompt, '{tone}') !== false) {
            $form->setField(new \SimpleXMLElement('<field type="text" labelclass="form-label small" class="route66-ai-input form-control-sm" label="COM_ROUTE66_PROMPT_INPUT_TONE_LABEL" name="tone" />'), null, true, 'route66-prompt-fields');
        }

        return $form;
    }
}
