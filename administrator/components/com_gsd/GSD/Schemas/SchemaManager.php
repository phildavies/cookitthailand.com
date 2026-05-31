<?php

/**
 * @package         Google Structured Data
 * @version         6.2.0 Free
 *
 * @author          Tassos Marinos <info@tassos.gr>
 * @link            http://www.tassos.gr
 * @copyright       Copyright © 2026 Tassos Marinos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
 */

namespace GSD\Schemas;

// No direct access
defined('_JEXEC') or die;

use GSD\Helper;
use Joomla\CMS\Language\Text;

/**
 * SchemaManager is a simple singleton that collects schema objects (or
 * arrays/strings) and renders them into JSON-LD script blocks. It
 * centralises merging logic for schema types that must not appear
 * multiple times and provides deduplication by hashing the added schemas.
 *
 * Usage:
 *  $sm = SchemaManager::getInstance();
 *  $sm->addSchema($schema)->render();
 */
class SchemaManager
{
    /**
     * Holds the single instance of the SchemaManager.
     *
     * @var SchemaManager|null
     */
    private static ?SchemaManager $instance = null;

    /**
     * Collected schema items keyed by their hash. Each item may be:
     * - an object implementing getContent()/merge()/allowMultipleScripts(),
     * - an associative array representing a schema,
     * - or a string containing already-rendered JSON/markup.
     *
     * @var array<string, mixed>
     */
    protected array $schemas = [];

    /**
     * Private constructor to prevent direct instantiation (singleton).
     */
    private function __construct() {}

    /**
     * Get the single instance of the SchemaManager.
     *
     * @return SchemaManager The singleton instance.
     */
    public static function getInstance(): SchemaManager
    {
        if (self::$instance === null)
        {
            self::$instance = new self();
        }

        return self::$instance;
    }

    /**
     * Add a schema to the collection.
     *
     * This method is intentionally fluent so multiple calls can be chained.
     * It ignores null/empty values and deduplicates schemas using a hash of
     * the serialized item.
     * 
     * @param mixed $schema Object/array/string schema to add.
     * 
     * @return $this
     */
    public function addSchema($schema)
    {
        if (is_null($schema) || empty($schema))
        {
            return $this;
        }

        $hash = md5(serialize($schema));

        if (isset($this->schemas[$hash]))
        {
            return $this;
        }

        $this->schemas[$hash] = $schema;

        return $this;
    }

    /**
     * Return all collected schema objects.
     *
     * @return array<string, mixed> Keyed collection of schemas.
     */
    public function getSchemas(): array
    {
        return $this->schemas;
    }

    /**
     * Merge, format and render collected schemas into HTML script blocks
     * ready to be injected into a document head or body. 
     * 
     * The method will:
     * - skip empty collections and return null,
     * - convert non-object schema entries directly to JSON-LD,
     * - for objects, call getContent() and, if required, merge with
     *   an existing schema of the same @type using merge(), respecting
     *   allowMultipleScripts() when deciding whether to create a new
     *   script block or merge into an existing one.
     *
     * @return string|null HTML script blocks or null when nothing to render.
     */
    public function render()
    {
        if (empty($this->schemas))
        {
            return;
        }

        $schemaArrays = [];
        $scripts = [];

        // Iterate collected items and normalize them into either
        // - $schemaArrays: arrays that will be encoded as JSON-LD, or
        // - $scripts: already-rendered strings (for custom code etc.)
        foreach ($this->schemas as $schema)
        {
            // Skip falsy entries (defensive)
            if (!$schema)
            {
                continue;
            }

            // Non-object values (arrays) are appended directly.
            // We keep this for backwards compatibility, but ideally
            // all schemas should be objects implementing the Schemas\Base class.
            if (!is_object($schema))
            {
                $schemaArrays[] = $schema;
                continue;
            }

            // Ask the object for its content – expected to return array or string
            $schemaArray = $schema->getContent();

            // Skip falsy entries (defensive)
            if (is_null($schemaArray) || empty($schemaArray))
            {
                continue;
            }   

            // Special case: some schemas may return a string that's already
            // fully rendered JSON/markup (custom code). Collect these in $scripts.
            if (is_string($schemaArray))
            {
                $scripts[] = $schemaArray;
                continue;
            }

            // If the schema object does not allow multiple scripts of the same
            // @type we must try to find an existing array with the same @type
            // and merge them together instead of creating duplicate script
            // blocks.
            if (!$schema->allowMultipleScripts())
            {
                $existingIndex = null;
                foreach ($schemaArrays as $index => $existingSchema)
                {
                    if (is_array($existingSchema) && isset($existingSchema['@type']) && isset($schemaArray['@type']) && $existingSchema['@type'] === $schemaArray['@type'])
                    {
                        $existingIndex = $index;
                        break;
                    }
                }

                if ($existingIndex !== null)
                {
                    // Merge current schema into the previously collected one
                    $schemaArrays[$existingIndex] = $schema->merge($schemaArrays[$existingIndex], $schemaArray);
                    continue; // Skip adding a new entry
                }
            }

            // Default behaviour: append the schema array as new entry
            $schemaArrays[] = $schemaArray;
        }

        foreach ($schemaArrays as $schemaArray)
        {
            // Sanity check: skip non-arrays
            if (!is_array($schemaArray))
            {
                continue;
            }

            // In case we have an array, transform it into JSON-LD format.
            // Always prepend the @context property
            $schemaArray = ['@context' => 'https://schema.org'] + $schemaArray;

            // We do not use JSON_NUMERIC_CHECK here because it doesn't respect numbers starting with 0. 
            // Bug: https://bugs.php.net/bug.php?id=70680
            $schemaJSON = json_encode($schemaArray, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

            // Detect issues with the encoding
            if (json_last_error() !== JSON_ERROR_NONE)
            {
                $schemaJSON = Text::sprintf('JSON Error: %s.', json_last_error_msg()) . ' ' . $schemaArray;
            }

            $scripts[] = <<<HTML
            <script type="application/ld+json" data-type="gsd">
            {$schemaJSON}
            </script>
            HTML;
        }

        // Convert data array to string
		$markup = implode("\n", array_filter($scripts));

		// Minify output
		if (Helper::getParams()->get('minifyjson', false))
		{
			$markup = Helper::minify($markup);
		}

		Helper::log($markup);

        $label = Text::_('GSD');

        return <<<HTML
        <!-- Start: $label -->
        $markup
        <!-- End: $label -->
        HTML;
    }
}