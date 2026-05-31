<?php
/*
 * Copyright (c) 2017-2025 Aimy Extensions, Netzum Sorglos Software GmbH
 * Copyright (c) 2014-2017 Aimy Extensions, Lingua-Systems Software GmbH
 *
 * https://www.aimy-extensions.com/
 *
 * License: GNU GPLv2, see LICENSE.txt within distribution and/or
 *          https://www.aimy-extensions.com/software-license.html
 */
 defined( '_JEXEC' ) or die(); use Joomla\CMS\Factory; use Joomla\CMS\Language\Text; $i = 0; $class_row = 'row-fluid'; $class_span = 'span'; switch ( $this->container_style ) { case 'bs3': $class_row = 'row'; $class_span = 'col-md-'; break; } if ( $this->show_credits ) { $align = ''; if ( $this->variant == 'index' ) { $align = 'text-align: center;'; } Factory::getDocument()->addStyleDeclaration( '#aimy-sitemap-credits' . '{' . 'margin-top: 12px;' . $align . '}' . '#aimy-sitemap-credits a,' . '#aimy-sitemap-credits a:link,' . '#aimy-sitemap-credits a:active,' . '#aimy-sitemap-credits a:visited' . '{' . 'opacity: 0.75;' . '}' ); } ?>

<div itemscope="itemscope" itemtype="http://schema.org/Article">



<?php if ( $this->params->get( 'show_page_heading', false ) && $this->params->get( 'page_heading', false ) ) : ?>

<h1 itemprop="name"><?php
 echo htmlspecialchars( $this->params->get( 'page_heading', '' ) ); ?></h1>

<?php endif; ?>


<div class="aimysitemap<?php
 echo htmlspecialchars( $this->params->get( 'pageclass_sfx', '' ) ); ?>" itemprop="articleBody">

<?php if ( $this->variant == 'index' ) : ?>

<div id="aimysitemap-index">

    <?php foreach ( array_keys( $this->data ) as $i => $key ) : ?>

    <?php
 if ( $i % 3 == 0 ) { echo '<div class="', $class_row, '">'; } ?>

    <div class="<?php echo $class_span, '4'; ?>">

    <h2><?php echo $key; ?></h2>

    <ul>
        <?php foreach ( $this->data[ $key ] as $item ) : ?>
        <li><a href="<?php
 echo $item->href; ?>" title="<?php
 echo $item->href; ?>"><?php
 echo $item->title; ?></a></li>
        <?php endforeach; ?>
    </ul>

    </div>

    <?php
 if ( $i % 3 == 2 ) { echo '</div>'; } ?>

    <?php endforeach; ?>

    <?php
 if ( $i == 0 or $i % 3 != 2 ) { echo '</div>'; } ?>

</div><!-- /#aimysitemap-index -->

<?php elseif ( $this->variant == 'hierarchy' ) : ?>

<div id="aimysitemap-hierarchy">

    <?php
 $this->data->render( function ( $ctx, $lvl ) { echo ( $ctx == 'open' ? '<ul class="aimysitemap-lvl-' . $lvl . '">' : '</ul>' ); }, function ( $ctx, $lvl, &$item ) { if ( $ctx == 'close' ) { echo '</li>'; return; } echo '<li>', '<a href="', $item->href, '" ', 'title="', $item->href, '"', ( $item->lang !== '*' ? ' class="aimysitemap-lang-' . strtolower($item->lang) . '"' : '' ), '>', $item->title, '</a>'; } ); ?>


</div><!-- /#aimysitemap-hierarchy -->

<?php elseif ( $this->variant == 'priority' ) : ?>

<div id="aimysitemap-priority">

    <ul>
    <?php foreach ( array_values( $this->data ) as $item ) : ?>
        <li><a href="<?php
 echo $item->href; ?>" title="<?php
 echo $item->href; ?>"><?php
 echo $item->title; ?></a></li>
    <?php endforeach; ?>
    </ul>

</div><!-- /#aimysitemap-priority -->

<?php else : ?>

<div id="aimysitemap-list">

    <ul>
    <?php foreach ( array_values( $this->data ) as $item ) : ?>
        <li><a href="<?php
 echo $item->href; ?>" title="<?php
 echo $item->href; ?>"><?php
 echo $item->title; ?></a></li>
    <?php endforeach; ?>
    </ul>

</div><!-- /#aimysitemap-list -->

<?php endif; ?>

<?php if ( $this->show_credits ) : ?>
<p id="aimy-sitemap-credits">
    <a href="https://www.aimy-extensions.com/joomla/sitemap.html" target="_blank" rel="noopener"><?php
 echo Text::sprintf( 'AIMY_SM_GENERATED_BY_X', 'Aimy Sitemap' ); ?></a>
</p>
<?php endif; ?>

</div>
</div>

<?php
 
