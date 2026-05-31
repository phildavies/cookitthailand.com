<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Controller;

use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Response\JsonResponse;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

class UpgradeController extends BaseController
{
    public function run()
    {
        $this->checkToken();

        $db = Factory::getDbo();

        $id   = $this->input->getInt('id');
        $step = $this->input->getString('step');

        $limit = 50;

        if ($step === 'metadata') {

            $query = $db->getQuery(true);
            $query->select('*')->from('#__route66_metadata_backup')->where('id > '.$id)->order('id ASC');
            try {
                $db->setQuery($query, 0, $limit);
                $rows = $db->loadObjectList();
            } catch (\Throwable $th) {
                echo new JsonResponse(['id' => 0, 'step' => 'metadata', 'completed' => false], $th->getMessage(), true);
                return $this;
            }

            if (!\count($rows)) {
                echo new JsonResponse(['id' => 0, 'step' => 'scores', 'completed' => false]);
                return $this;
            }

            foreach ($rows as $row) {
                $metadata               = json_decode($row->metadata);
                $record                 = new \stdClass();
                $record->resource_id    = $row->context.'.'.$row->resourceId;
                $record->og_title       = $metadata->og_title;
                $record->og_description = $metadata->og_description;
                $record->og_type        = $metadata->og_type;
                $record->og_image       = $metadata->og_image_media;

                if ($metadata->og_image === 'intro_image' || $metadata->og_image === 'full_image') {
                    $images = $this->getArticleImages($row->resourceId);
                    if ($images) {
                        if ($metadata->og_image === 'intro_image') {
                            $record->og_image = $images->image_intro;
                        } elseif ($metadata->og_image === 'full_image') {
                            $record->og_image = $images->image_fulltext;
                        }
                    }
                }

                try {
                    $query = $db->getQuery(true);
                    $query->delete('#__route66_metadata')->where('resource_id = '.$db->q($record->resource_id));
                    $db->setQuery($query);
                    $db->execute();

                    $db->insertObject('#__route66_metadata', $record);
                } catch (\Throwable $th) {
                }

            }

            echo new JsonResponse(['id' => $row->id, 'step' => 'metadata', 'completed' => false]);
            return $this;

        } elseif ($step === 'scores') {

            $query = $db->getQuery(true);
            $query->select('*')->from('#__route66_seo')->where('id > '.$id)->order('id ASC');

            try {
                $db->setQuery($query, 0, $limit);
                $rows = $db->loadObjectList();
            } catch (\Throwable $th) {
                echo new JsonResponse(['id' => 0, 'step' => 'scores', 'completed' => false], $th->getMessage(), true);
                return $this;
            }

            if (!\count($rows)) {
                echo new JsonResponse(['id' => 0, 'step' => 'scores', 'completed' => true]);
                return $this;
            }

            foreach ($rows as $row) {
                $record                    = new \stdClass();
                $record->resource_id       = $row->context.'.'.$row->resourceId;
                $record->seo_keyphrase     = $row->keyword;
                $record->seo_score         = $row->score;
                $record->readability_score = $row->readability;

                try {
                    $db->insertObject('#__route66_content_analysis', $record);
                } catch (\Throwable $th) {
                }
            }

            echo new JsonResponse(['id' => $row->id, 'step' => 'scores', 'completed' => false]);
            return $this;

        }

        return $this;
    }

    private function getArticleImages($id)
    {
        $db    = Factory::getDbo();
        $query = $db->getQuery(true);
        $query->select('images')->from('#__content')->where('id = '.$db->q($id));
        $db->setQuery($query);
        $result = $db->loadResult();

        if (!$result) {
            return null;
        }

        $images = json_decode($result);

        if (!\is_object($images)) {
            return null;
        }

        return $images;
    }
}
