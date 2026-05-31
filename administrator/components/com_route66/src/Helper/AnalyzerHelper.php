<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Helper;

use Firecoders\Component\Route66\Administrator\Analyzer\Analyzer;
use Joomla\CMS\Factory;
use Joomla\CMS\Plugin\PluginHelper;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class AnalyzerHelper
{
    public static function shouldDisplay(): bool
    {
        $application = Factory::getApplication();

        if (!$application->isClient('administrator')) {
            return false;
        }

        if ($application->input->getMethod() !== 'GET') {
            return false;
        }

        $document = Factory::getDocument();

        if ($document->getType() !== 'html') {
            return false;
        }

        $user = $application->getIdentity();

        if ($user->guest) {
            return false;
        }

        if (\defined('ROUTE66_ANALYZER_DISPLAYED') && ROUTE66_ANALYZER_DISPLAYED === true) {
            return false;
        }

        PluginHelper::importPlugin('route66');
        $result = $application->triggerEvent('onRoute66AnalyzerDisplay');

        $render = $result && $result === true;

        if ($render) {
            \define('ROUTE66_ANALYZER_DISPLAYED', true);
        }

        return $render;
    }

    public static function shouldSave(): bool
    {
        $application = Factory::getApplication();

        if (!$application->isClient('administrator')) {
            return false;
        }

        if ($application->input->getMethod() !== 'POST') {
            return false;
        }

        $user = $application->getIdentity();

        if ($user->guest) {
            return false;
        }

        if (\defined('ROUTE66_ANALYZER_SAVED') && ROUTE66_ANALYZER_SAVED === true) {
            return false;
        }

        PluginHelper::importPlugin('route66');
        $result = $application->triggerEvent('onRoute66AnalyzerSave');

        $save = $result && $result === true;

        if ($save) {
            \define('ROUTE66_ANALYZER_SAVED', true);
        }

        return $save;
    }

    public static function display(string $position = '', $form = null): void
    {
        $analyzer = new Analyzer(['form' => $form]);

        if ($position === 'form') {
            $analyzer->displayInForm();
        } elseif ($position === 'toolbar') {
            $analyzer->displayInToolbar();
        }
    }

    public static function save()
    {
        $application = Factory::getApplication();

        $jform = $application->input->get('jform');

        if (!$jform) {
            return;
        }

        if (!\is_array($jform) || !isset($jform['route66'])) {
            return;
        }

        $data = $application->input->getArray([
            'jform' => [
                'route66' => [
                    'metadata' => [
                        'id'             => 'INT',
                        'resource_id'    => 'STRING',
                        'title'          => 'STRING',
                        'description'    => 'STRING',
                        'robots'         => 'STRING',
                        'canonical'      => 'STRING',
                        'og_title'       => 'STRING',
                        'og_description' => 'STRING',
                        'og_image'       => 'STRING',
                        'og_type'        => 'STRING',
                        'customize_x'    => 'INT',
                        'x_title'        => 'STRING',
                        'x_description'  => 'STRING',
                        'x_image'        => 'STRING',
                    ],
                    'analysis' => [
                        'id'                => 'INT',
                        'resource_id'       => 'STRING',
                        'seo_keyphrase'     => 'STRING',
                        'seo_score'         => 'INT',
                        'readability_score' => 'INT',
                    ],
                ],
            ],
        ]);

        $metadata      = $data['jform']['route66']['metadata'];
        $metadataModel = $application->bootComponent('com_route66')->getMVCFactory()->createModel('Metadata', 'Administrator', ['ignore_request' => true]);
        $metadataModel->save($metadata);

        $analysis             = $data['jform']['route66']['analysis'];
        $contentAnalysisModel = $application->bootComponent('com_route66')->getMVCFactory()->createModel('ContentAnalysis', 'Administrator', ['ignore_request' => true]);
        $contentAnalysisModel->save($analysis);
    }

    public static function scoreToRating($score): string
    {
        if (!$score) {
            return '';
        }

        $score = $score / 10;

        if ($score <= 4) {
            return 'bad';
        }

        if ($score > 4 && $score <= 7) {
            return 'ok';
        }

        if ($score > 7) {
            return 'good';
        }
    }

    public static function ratingToScoreRange($rating): array
    {
        if (!$rating) {
            return [];
        }

        if ($rating === 'bad') {
            return [0, 40];
        } elseif ($rating === 'ok') {
            return [40, 70];
        } elseif ($rating === 'good') {
            return [70, 100];
        }

        return [];
    }
}
