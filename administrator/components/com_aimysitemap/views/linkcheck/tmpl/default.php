<?php
/*
 * Copyright (c) 2017-2025 Aimy Extensions, Netzum Sorglos Software GmbH
 * Copyright (c) 2016-2017 Aimy Extensions, Lingua-Systems Software GmbH
 *
 * https://www.aimy-extensions.com/
 *
 * License: GNU GPLv2, see LICENSE.txt within distribution and/or
 *          https://www.aimy-extensions.com/software-license.html
 */
 defined( '_JEXEC' ) or die(); use Joomla\CMS\Language\Text; use Joomla\CMS\Router\Route; use Joomla\CMS\HTML\HTMLHelper; $limitstart = isset( $this->pagination->limitstart ) ? $this->pagination->limitstart : 0; ?>
<div id="j-main-container" class="j-main-container aimy-main clearfix">

<div class="row-fluid" id="aimy-linkcheck-container">

<h1><?php echo Text::_( 'AIMY_SM_LINK_LINKCHECK' ); ?></h1>


<div id="aimy-pro-feature-container">
<p class="aimy-pro-feature">
     <a href="https://www.aimy-extensions.com/joomla/sitemap.html" target="_blank" rel="noopener"><?php
 echo Text::_( 'AIMY_SM_PRO_FEATURE' ); ?></a>
</p>
</div>


</div><!-- /.row-fluid -->
</div>

<?php
 include( JPATH_ADMINISTRATOR . '/components/com_aimysitemap/helpers/footer.php' ); 
