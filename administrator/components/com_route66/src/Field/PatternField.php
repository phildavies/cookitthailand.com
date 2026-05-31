<?php

/**
 * @author      Lefteris Kavadas
 * @copyright   Copyright (c) 2016 - 2025 Lefteris Kavadas / firecoders.com
 * @license     GNU General Public License version 3 or later
 */

namespace Firecoders\Component\Route66\Administrator\Field;

\defined('_JEXEC') or die;

use Joomla\CMS\Form\Field\TextField;

class PatternField extends TextField
{
    protected $type = 'Pattern';

    public function setup(\SimpleXMLElement $element, $value, $group = null)
    {
        if (!parent::setup($element, $value, $group)) {
            return false;
        }

        $this->description = $this->getDescription();

        return true;
    }

    private function getDescription()
    {
        $tokens      = explode(',', (string) $this->element['tokens']);
        $identifiers = explode(',', (string) $this->element['identifiers']);

        $items = [];

        foreach ($tokens as $token) {
            if (\in_array($token, $identifiers)) {
                $items[] = '<strong>' . $token . '</strong>';
            } else {
                $items[] = $token;
            }
        }

        return '<div class="mt-2 text-muted"><em>' . implode(', ', $items) . '</em></div>';
    }
}
