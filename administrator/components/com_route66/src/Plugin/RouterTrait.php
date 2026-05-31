<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2024 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Plugin;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Language\LanguageHelper;
use Joomla\CMS\Language\Multilanguage;
use Joomla\Event\Event;

\defined('_JEXEC') or die;

trait RouterTrait
{
    use ExtensionTrait;
    use ModelTrait;

    public function onRouterRules(Event $event): void
    {
        if (!$this->isInstalled()) {
            return;
        }

        $result = [];

        $params = ComponentHelper::getParams('com_route66');

        foreach (self::RULES as $rule) {

            $patterns = $params->get(strtolower('patterns.com_'.$this->_name.'_'.$rule));

            if (!\is_object($patterns)) {
                continue;
            }

            foreach ($patterns as $language => $pattern) {

                if (!$pattern) {
                    continue;
                }

                if (Multilanguage::isEnabled() && $language === '*') {
                    continue;
                }

                if (!Multilanguage::isEnabled() && $language !== '*') {
                    continue;
                }

                $className = '\\Firecoders\\Plugin\\Route66\\'.ucfirst($this->_name).'\\Router\\'.ucfirst($rule);
                $model     = $this->getModel('Router');
                $result[]  = new $className($pattern, $language, $model);
            }
        }

        $event->setArgument('result', array_merge($event->getArgument('result', []), $result));
    }

    public function onRouterForm(Event $event): void
    {
        if (!$this->isInstalled()) {
            return;
        }

        $rules = self::RULES;

        if (!\count($rules)) {
            return;
        }

        [$form] = $event->getArguments();

        // Create fieldset
        $fieldset = 'com_'.strtolower($this->_name).'_patterns';
        $form->setField(new \SimpleXMLElement('<fieldset name="'.$fieldset.'" label="COM_ROUTE66_PATTERNS_FIELDSET_'.strtoupper($this->_name).'"></fieldset>'));

        $languages = array_merge(['*'], array_keys(LanguageHelper::getLanguages('lang_code')));

        foreach ($rules as $rule) {

            // Get tokens and identifiers
            $className   = '\\Firecoders\\Plugin\\Route66\\'.ucfirst($this->_name).'\\Router\\'.ucfirst($rule);
            $tokens      = implode(',', array_keys(\constant($className.'::TOKENS')));
            $identifiers = implode(',', \constant($className.'::IDENTIFIERS'));

            // Create group
            $group = 'com_'.strtolower($this->_name.'_'.$rule);
            $form->setField(new \SimpleXMLElement('<fields name="'.$group.'"></fields>'), '', true, $fieldset);

            // We need one field per language including any (*) language
            foreach ($languages as $index => $language) {

                // Create field
                $name  = $language;
                $form->setField(new \SimpleXMLElement('<field type="pattern" name="'.$name.'" />'), $group, true, $fieldset);

                // Modify field attributes
                $form->setFieldAttribute($name, 'label', 'COM_ROUTE66_PATTERN_'.strtoupper($this->_name.'_'.$rule), $group);
                $form->setFieldAttribute($name, 'validate', 'pattern', $group);
                $form->setFieldAttribute($name, 'tokens', $tokens, $group);
                $form->setFieldAttribute($name, 'identifiers', $identifiers, $group);

                // Hide * language when multilanguage is enabled
                if (Multilanguage::isEnabled() && $language === '*') {
                    $form->setFieldAttribute($name, 'type', 'hidden', $group);
                }

                // Hide regular languages when multilanguage is disabled
                if (!Multilanguage::isEnabled() && $language !== '*') {
                    $form->setFieldAttribute($name, 'type', 'hidden', $group);
                }

                // Display addon in regular languages
                if ($language !== '*') {
                    $form->setFieldAttribute($name, 'addonBefore', $language, $group);
                    if ($index > 1) {
                        $form->setFieldAttribute($name, 'labelclass', 'hidden', $group);
                    }
                }
            }
        }
    }
}
