<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Firecoders\Component\Route66\Administrator\Helper\Route66Helper;
use Joomla\CMS\Language\Text;

?>

<div class="container">

  <div class="row mb-4">
    <div class="col-12">
      <h2 class="display-6"><?php echo Text::_('COM_ROUTE66_SEO_DASHBOARD'); ?></h2>
      <p class="text-muted"><?php echo Text::_('COM_ROUTE66_OVERVIEW_DESCRIPTION'); ?></p>
    </div>
  </div>

  <!-- Site Health Summary -->
  <div class="row mb-4">
    <div class="col-md-3">
      <div class="card text-bg-success mb-2">
        <div class="card-body">
          <h5 class="card-title"><?php echo Text::_('COM_ROUTE66_GOOD_PAGES'); ?></h5>
          <p class="display-6"><?php echo $this->goodPages; ?></p>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <a href="<?php echo $this->pagesWithIssuesLink; ?>" class="text-decoration-none text-reset">
      <div class="card text-bg-warning mb-2">
        <div class="card-body">
          <h5 class="card-title"><?php echo Text::_('COM_ROUTE66_PAGES_WITH_ISSUES'); ?></h5>
          <p class="display-6"><?php echo $this->pagesWithIssues; ?></p>
        </div>
      </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="<?php echo $this->pagesWithPerformanceIssuesLink; ?>" class="text-decoration-none text-reset">
      <div class="card text-bg-danger mb-2">
        <div class="card-body">
          <h5 class="card-title"><?php echo Text::_('COM_ROUTE66_PAGES_WITH_PERFORMANCE_ISSUES'); ?></h5>
          <p class="display-6"><?php echo $this->pagesWithPerformanceIssues; ?></p>
        </div>
      </div>
      </a>
    </div>
    <div class="col-md-3">
      <a href="<?php echo $this->totalPagesLink; ?>" class="text-decoration-none text-reset">
      <div class="card text-bg-info mb-2">
        <div class="card-body">
          <h5 class="card-title"><?php echo Text::_('COM_ROUTE66_TOTAL_PAGES'); ?></h5>
          <p class="display-6"><?php echo $this->totalPages; ?></p>
        </div>
      </div>
      </a>
    </div>
  </div>

  <div class="row g-4 mb-4">

    <div class="col-md-12">
      <div class="card">
        <h2 class="card-header fs-4"><?php echo Text::_('COM_ROUTE66_DETECTED_SEO_ISSUES'); ?></h2>
        <ul class="list-group list-group-flush">
          <?php foreach ($this->issues as $issue): ?>
          <li class="list-group-item d-flex align-items-center">
            <a class="flex-grow-1" href="index.php?option=com_route66&amp;view=pages&amp;filter[issues][]=<?php echo $issue->filter; ?>&amp;filter[response_type]=normal">
              <?php echo $issue->title; ?>
              <span class="menu-badge">
                <span class="float-end badge bg-<?php echo $issue->total > 0 ? $issue->type : 'success'; ?>">
                  <?php echo $issue->total; ?>
                </span>
              </span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div class="col-md-6">
       <div class="card">
        <h2 class="card-header fs-4"><?php echo Text::_('COM_ROUTE66_CONTENT_SEO_SCORE_DISTRIBUTION'); ?></h2>
        <ul class="list-group list-group-flush">
          <?php foreach ($this->seo as $seo): ?>
          <li class="list-group-item d-flex align-items-center">
            <a class="flex-grow-1" href="index.php?option=com_route66&amp;view=pages&amp;filter[seo_rating][]=<?php echo $seo->filter; ?>&amp;filter[response_type]=normal">
              <?php echo $seo->title; ?>
              <span class="menu-badge">
                <span class="float-end badge bg-<?php echo $seo->type; ?>">
                  <?php echo $seo->total; ?>
                </span>
              </span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card">
        <h2 class="card-header fs-4"><?php echo Text::_('COM_ROUTE66_CONTENT_READABILITY_SCORE_DISTRIBUTION'); ?></h2>
        <ul class="list-group list-group-flush">
          <?php foreach ($this->readability as $readability): ?>
          <li class="list-group-item d-flex align-items-center">
            <a class="flex-grow-1" href="index.php?option=com_route66&amp;view=pages&amp;filter[readability_rating][]=<?php echo $readability->filter; ?>&amp;filter[response_type]=normal">
              <?php echo $readability->title; ?>
              <span class="menu-badge">
                <span class="float-end badge bg-<?php echo $readability->type; ?>">
                  <?php echo $readability->total; ?>
                </span>
              </span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

  </div>

  <div class="row">
    <div class="col-12">

  <div class="card">
    <h2 class="card-header fs-4"><?php echo Text::_('COM_ROUTE66_QUICK_ACTIONS'); ?></h2>
    <div class="card-body">
      <div class="row g-3">
        <div class="col-md-3">
          <a href="index.php?option=com_route66&view=pages" class="btn btn-primary w-100"><?php echo Text::_('COM_ROUTE66_PAGES'); ?></a>
        </div>
        <div class="col-md-3">
          <a href="index.php?option=com_route66&view=redirects" class="btn btn-primary w-100"><?php echo Text::_('COM_ROUTE66_AI_TOOLS'); ?></a>
        </div>
        <div class="col-md-3">
          <a href="index.php?option=com_route66&view=sitemaps" class="btn btn-primary w-100"><?php echo Text::_('COM_ROUTE66_SITEMAPS'); ?></a>
        </div>
        <div class="col-md-3">
          <a href="index.php?option=com_route66&view=analyzer" class="btn btn-primary w-100"><?php echo Text::_('COM_ROUTE66_EDIT_ROBOTS_TXT'); ?></a>
        </div>
      </div>
    </div>
  </div>
  </div>

  </div>


</div>

 <?php echo Route66Helper::copyrights(); ?>

