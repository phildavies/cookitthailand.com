<?php
/**
 * @package	HikaShop for Joomla!
 * @version	6.1.1
 * @author	hikashop.com
 * @copyright	(C) 2010-2025 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

use Joomla\CMS\Event\Editor\EditorButtonsSetupEvent;
use Joomla\Event\SubscriberInterface;

abstract class plgButtonHikashopproductBridge extends hikashopJoomlaPlugin implements SubscriberInterface
{
    public static function getSubscribedEvents(): array
    {
        return ['onEditorButtonsSetup' => 'onEditorButtonsSetup'];
    }
	public function onEditorButtonsSetup(EditorButtonsSetupEvent $event): void
    {
        $subject  = $event->getButtonsRegistry();
        $disabled = $event->getDisabledButtons();

        if (\in_array($this->_name, $disabled)) {
            return;
        }

        $button = $this->onDisplay($event->getEditorId());

        if ($button) {
            $subject->add($button);
        }
    }
}
