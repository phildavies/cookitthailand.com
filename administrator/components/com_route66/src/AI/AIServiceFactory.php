<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\AI;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Component\ComponentHelper;

class AIServiceFactory
{
    public static function create(): AIServiceInterface|null
    {
        $params = ComponentHelper::getParams('com_route66');

        $service = $params->get('ai_service', 'none');

        switch ($service) {
            case 'openai':
                $apiKey = trim($params->get('openai_api_key', ''));
                $model  = $params->get('openai_model', 'gpt-4-1-mini');

                return $apiKey ? new OpenAIService($apiKey, $model) : null;

            case 'anthropic':
                $apiKey = trim($params->get('anthropic_api_key', ''));
                $model  = $params->get('anthropic_model', 'claude-3-5-haiku-latest');

                return $apiKey ? new AnthropicService($apiKey, $model) : null;

            case 'google':
                $apiKey = trim($params->get('google_api_key', ''));
                $model  = $params->get('google_model', 'gemini-2.5-flash');

                return $apiKey ? new GoogleService($apiKey, $model) : null;

            default:
                return null;
        }
    }
}
