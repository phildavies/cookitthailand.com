<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Helper;

use Firecoders\Component\Route66\Administrator\Issues\DuplicateDescriptionIssue;
use Firecoders\Component\Route66\Administrator\Issues\DuplicateResourceIssue;
use Firecoders\Component\Route66\Administrator\Issues\DuplicateTitleIssue;
use Firecoders\Component\Route66\Administrator\Issues\ExcessiveDOMSizeIssue;
use Firecoders\Component\Route66\Administrator\Issues\NoCompressionIssue;
use Firecoders\Component\Route66\Administrator\Issues\NoDescriptionIssue;
use Firecoders\Component\Route66\Administrator\Issues\NoFollowIssue;
use Firecoders\Component\Route66\Administrator\Issues\NoIndexIssue;
use Firecoders\Component\Route66\Administrator\Issues\NoTitleIssue;
use Firecoders\Component\Route66\Administrator\Issues\PageSizeIssue;
use Firecoders\Component\Route66\Administrator\Issues\PageTimeIssue;
use Firecoders\Component\Route66\Administrator\Issues\RobotsTxtBlockedIssue;

\defined('_JEXEC') or die;

class IssuesHelper
{
    private static array $issueClasses = [
        NoTitleIssue::class,
        NoDescriptionIssue::class,
        NoCompressionIssue::class,
        DuplicateTitleIssue::class,
        DuplicateDescriptionIssue::class,
        DuplicateResourceIssue::class,
        NoIndexIssue::class,
        NoFollowIssue::class,
        RobotsTxtBlockedIssue::class,
        PageTimeIssue::class,
        PageSizeIssue::class,
        ExcessiveDOMSizeIssue::class,
    ];

    public static function check(object $page): array
    {
        $issues = [];

        foreach (self::$issueClasses as $issueClass) {
            $issue = new $issueClass($page);
            if ($issue->isDetected()) {
                $issues[] = (object) [
                    'label'       => $issue->getLabel(),
                    'description' => $issue->getDescription(),
                    'type'        => $issue->getType(),
                ];
            }
        }

        return $issues;
    }

    public static function get()
    {
        $issues = [];

        foreach (self::$issueClasses as $issueClass) {
            $issues[] = new $issueClass((object)[]);
        }

        return $issues;
    }
}
