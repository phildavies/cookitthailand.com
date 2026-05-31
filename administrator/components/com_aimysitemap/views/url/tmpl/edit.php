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
 defined( '_JEXEC' ) or die(); require_once( JPATH_COMPONENT . '/helpers/compat.php' ); use Joomla\CMS\HTML\HTMLHelper; use Joomla\CMS\Router\Route; use Joomla\CMS\Language\Text; $fields = array( 'id', 'title', 'url', 'priority', 'changefreq', 'lang', 'state', 'lock', 'mtime' ); HTMLHelper::_( 'behavior.core' ); HTMLHelper::_( 'behavior.formvalidator' ); if ( AimySitemapCompatHelper::isJoomla3() ) { HTMLHelper::_( 'formbehavior.chosen', 'select' ); } ?>

<form action="<?php
 echo Route::_( 'index.php?option=com_aimysitemap&layout=edit&' . 'id=' . (int) $this->item->id ); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
    <div class="row-fluid">
        <div class="span-12 form-horizontal">
        <fieldset>
            <?php
 echo HTMLHelper::_( AimySitemapCompatHelper::getJHtmlBootstrapMethodName( 'startPane' ), 'myTab', array( 'active' => 'details' ) ); echo HTMLHelper::_( AimySitemapCompatHelper::getJHtmlBootstrapMethodName( 'addPanel' ), 'myTab', 'details', Text::sprintf( 'AIMY_SM_EDIT_URL_X', $this->item->id, true ) ); ?>
            <?php foreach ( $fields as $field ) : ?>
            <div class="control-group">
                <div class="control-label"><?php
 echo $this->form->getLabel( $field ); ?></div>
                <div class="controls"><?php
 echo $this->form->getInput( $field ); ?></div>
            </div>
            <?php endforeach; ?>
            <?php
 echo HTMLHelper::_( AimySitemapCompatHelper::getJHtmlBootstrapMethodName( 'endPanel' ) ); ?>

            <input type="hidden" name="task" value="" />
            <?php echo HTMLHelper::_( 'form.token' ); ?>

            <?php
 echo HTMLHelper::_( AimySitemapCompatHelper::getJHtmlBootstrapMethodName( 'endPane' ) ); ?>
        </fieldset>
    </div>
</form>

<?php
 
