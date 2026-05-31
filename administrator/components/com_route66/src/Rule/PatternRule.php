<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Rule;

// phpcs:disable PSR1.Files.SideEffects
\defined('_JEXEC') or die;
// phpcs:enable PSR1.Files.SideEffects

use Joomla\CMS\Form\Form;
use Joomla\CMS\Form\FormRule;
use Joomla\CMS\Language\Text;
use Joomla\Registry\Registry;

class PatternRule extends FormRule
{
    private $tokens;
    private $identifiers;

    public function test(\SimpleXMLElement $element, $value, $group = null, Registry $input = null, Form $form = null)
    {
        $this->identifiers = explode(',', (string) $element->attributes()->identifiers);
        $this->tokens      = array_merge($this->identifiers, explode(',', (string) $element->attributes()->tokens));

        if ($value) {
            $tokens  = [];
            $matches = [];
            preg_match_all('/{(.*?)}/', $value, $matches, PREG_SET_ORDER);

            if (\is_array($matches) && \count($matches)) {
                foreach ($matches as $match) {
                    if (!\in_array($match[1], $this->tokens)) {
                        return new \UnexpectedValueException(Text::sprintf('COM_ROUTE66_INVALID_TOKEN_USED', $match[1], Text::_($element->attributes()->label)));
                    }
                    $tokens[] = $match[1];
                }
                $tokens       = array_unique($tokens);
                $intersection = array_intersect($tokens, $this->identifiers);

                if (\count($intersection) == 0) {
                    return new \UnexpectedValueException(Text::sprintf('COM_ROUTE66_NO_IDENTIFIER_PROVIDED', Text::_($element->attributes()->label)));
                }
            } else {
                return new \UnexpectedValueException(Text::sprintf('COM_ROUTE66_INVALID_PATTERN', Text::_($element->attributes()->label)));
            }
        }


        return true;
    }
}
