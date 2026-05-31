<?php
/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

\defined('_JEXEC') or die;

use Joomla\CMS\Factory;

$wa = Factory::getApplication()->getDocument()->getWebAssetManager();
$wa->addInlineStyle('.route66-card {max-width: 940px;}');
?>


<div class="container my-3">

  <div class="d-flex flex-column align-items-center mb-5">
    <h2 class="fw-bold">Unlock AI Superpowers in Route 66 PRO</h2>
    <p class="lead">Boost your SEO workflow with advanced AI tools and customizable features</p>
    <div class="ratio ratio-16x9 shadow route66-card">
      <video controls autoplay loop muted playsinline class="object-fit-cover">
            <source src="https://www.firecoders.com/files/route66/upgrade/continue-writing.mp4" type="video/mp4">
      </video>
    </div>
  </div>

  <div class="d-flex flex-column align-items-center mb-5">
    <h2 class="fw-bold">Spend less time writing metadata and more time ranking</h2>
    <p class="lead">Start using AI in your workflow today</p>
    <div class="ratio ratio-16x9 shadow route66-card">
      <video controls autoplay loop muted playsinline class="object-fit-cover">
            <source src="https://www.firecoders.com/files/route66/upgrade/metadata.mp4" type="video/mp4">
      </video>
    </div>
  </div>

  <div class="d-flex flex-column align-items-center mb-5">
    <h2 class="fw-bold">Write smarter, not harder</h2>
    <p class="lead">Customize AI to sound just like you—or better</p>
    <div class="ratio ratio-16x9 shadow route66-card">
      <video controls autoplay loop muted playsinline class="object-fit-cover">
            <source src="https://www.firecoders.com/files/route66/upgrade/command.mp4" type="video/mp4">
      </video>
    </div>
  </div>


  <div class="d-flex flex-column align-items-center mb-5">
    <h2 class="fw-bold">Prebuilt AI Tools Ready to Use</h2>
    <p class="lead">Route 66 PRO includes a set of hand-crafted AI tools for common SEO tasks—ready to go out of the box</p>
    <div class="shadow route66-card">
      <img src="https://www.firecoders.com/files/route66/upgrade/ai-tools-modal.png" class="img-fluid"  alt="Core AI Tools Screenshot">
    </div>
  </div>

  <div class="d-flex flex-column align-items-center mb-5">
    <h2 class="fw-bold">Custom AI Tools & Prompt Tuning</h2>
    <p class="lead">Create your own AI tools and refine built-in prompts. Tailor AI behavior to your brand's voice or specific SEO goals</p>
    <div class="shadow route66-card">
      <img src="https://www.firecoders.com/files/route66/upgrade/ai-tools-edit.png" class="img-fluid" alt="Custom AI Tools Screenshot">
    </div>
  </div>

  <div class="d-flex flex-column align-items-center mb-5">
    <h2 class="fw-bold">AI Service & Model Selection</h2>
    <p class="lead">Choose the best AI model for your needs—optimize for cost, speed, or accuracy with OpenAI and Anthropic integrations.</p>
    <div class="shadow route66-card">
      <img src="https://www.firecoders.com/files/route66/upgrade/ai-tools-service-model-options.png" class="img-fluid" alt="AI Service Selection Screenshot">
    </div>
  </div>


  <div class="text-center m-5">
    <h4 class="fw-semibold">Ready to supercharge your SEO with AI?</h4>
    <p class="text-muted mb-2">Built for Joomla users who care about speed, structure, and smarter SEO.</p>
    <a href="https://www.firecoders.com/joomla-extensions/route-66" target="_blank" class="btn btn-success btn-lg px-4">
      Upgrade to Route 66 PRO Now
    </a>
    <p class="text-muted mt-2 mb-0"><small>One-year subscription. No automatic renewal. You’re in control.<br>AI Tools require an API key from OpenAI or Anthropic (additional charges may apply)</small></p>
  </div>

</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    const videos = document.querySelectorAll('.route66-card video');

    const observer = new IntersectionObserver(
      (entries) => {
        entries.forEach((entry) => {
          const video = entry.target;

          if (entry.isIntersecting) {
            // Pause all others
            videos.forEach((v) => {
              if (v !== video) v.pause();
            });

            // Play current
            video.play().catch(() => {});
          } else {
            // Pause when out of view
            video.pause();
          }
        });
      },
      {
        threshold: 0.75, // At least 75% in view
      }
    );

    videos.forEach((video) => observer.observe(video));
  });
</script>