<?php
/*
 * Copyright (c) 2017-2025 Aimy Extensions, Netzum Sorglos Software GmbH
 * Copyright (c) 2015-2017 Aimy Extensions, Lingua-Systems Software GmbH
 *
 * https://www.aimy-extensions.com/
 *
 * License: GNU GPLv2, see LICENSE.txt within distribution and/or
 *          https://www.aimy-extensions.com/software-license.html
 */
 defined( '_JEXEC' ) or die(); use Joomla\CMS\HTML\HTMLHelper; use Joomla\CMS\Language\Text; $jv = substr( JVERSION, 0, 1 ); ?>

<div id="j-main-container" class="j-main-container aimy-main clearfix">
<div id="aimy-periodic-crawl-container">


<h1><?php echo Text::_( 'AIMY_SM_LINK_PERIODIC' ); ?></h1>

<div id="aimy-pro-feature-container">
<p class="aimy-pro-feature">
    <a href="https://www.aimy-extensions.com/joomla/sitemap.html" target="_blank" rel="noopener"><?php
 echo Text::_( 'AIMY_SM_PRO_FEATURE' ); ?></a>
</p>
</div>


</div>
</div>

<?php
 include( JPATH_ADMINISTRATOR . '/components/com_aimysitemap/helpers/footer.php' ); 
