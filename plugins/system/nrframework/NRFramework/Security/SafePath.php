<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2026 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace Tassos\Framework\Security;

defined('_JEXEC') or die('Restricted access');

/**
 * Pure path validator: rejects traversal sequences and resolves relative
 * paths inside an allowed base. Never touches the filesystem beyond realpath().
 */
class SafePath
{
	/**
	 * Sanitize a relative path string. Rejects null bytes, .. segments and
	 * absolute paths.
	 *
	 * @param   string  $path  The path to sanitize
	 *
	 * @return  string  The path unchanged, if it passes all checks
	 *
	 * @throws  \InvalidArgumentException  If the path contains dangerous sequences
	 */
	public static function sanitize($path)
	{
		if (!is_string($path) || trim($path) === '')
		{
			throw new \InvalidArgumentException('Path must be a non-empty string');
		}

		// Reject null bytes
		if (strpos($path, "\0") !== false)
		{
			throw new \InvalidArgumentException('Path contains null byte');
		}

		// Reject .. segments (handles /, \, and mixed separators)
		if (preg_match('#(?:^|[\\\\/])\.\.(?:[\\\\/]|$)#', $path))
		{
			throw new \InvalidArgumentException('Path contains directory traversal sequence');
		}

		// Reject absolute paths
		if (self::isAbsolute($path))
		{
			throw new \InvalidArgumentException('Absolute paths are not allowed');
		}

		return $path;
	}

	/**
	 * Check whether a path is absolute.
	 *
	 * @param   string  $path
	 *
	 * @return  bool
	 */
	public static function isAbsolute($path)
	{
		if (!is_string($path) || $path === '')
		{
			return false;
		}

		// Unix absolute path
		if ($path[0] === '/' || $path[0] === '\\')
		{
			return true;
		}

		// Windows drive letter (e.g. C:\, D:/)
		if (strlen($path) >= 2 && ctype_alpha($path[0]) && $path[1] === ':')
		{
			return true;
		}

		return false;
	}

	/**
	 * True iff $absolutePath resolves to a location inside $baseDir
	 * (or is $baseDir itself). Uses realpath() so symlinks are followed.
	 *
	 * @param   string  $absolutePath  The absolute path to check
	 * @param   string  $baseDir       The base directory it must be within
	 *
	 * @return  bool
	 */
	public static function isWithin($absolutePath, $baseDir)
	{
		$realPath = realpath($absolutePath);
		$realBase = realpath($baseDir);

		if ($realPath === false || $realBase === false)
		{
			return false;
		}

		$ds = DIRECTORY_SEPARATOR;

		return $realPath === $realBase
			|| strpos($realPath, $realBase . $ds) === 0;
	}

	/**
	 * Sanitize $relative and resolve it inside $base.
	 *
	 * @param   string  $relative  A relative path (no .., no absolute, no null bytes)
	 * @param   string  $base      The base directory to resolve within
	 *
	 * @return  string  The resolved absolute path
	 *
	 * @throws  \InvalidArgumentException  If the path is unsafe
	 * @throws  \RuntimeException          If the resolved path escapes the base
	 */
	public static function absoluteWithin($relative, $base)
	{
		self::sanitize($relative);

		$absolute = \Joomla\Filesystem\Path::clean($base . DIRECTORY_SEPARATOR . $relative);

		if (!self::isWithin($absolute, $base))
		{
			throw new \RuntimeException('Resolved path is outside the allowed directory');
		}

		return realpath($absolute);
	}

	/**
	 * Strip $base from $absolute and return the relative remainder.
	 *
	 * @param   string  $absolute  The absolute path
	 * @param   string  $base      The base directory
	 *
	 * @return  string  The relative path
	 *
	 * @throws  \RuntimeException  If $absolute is not within $base
	 */
	public static function relativeTo($absolute, $base)
	{
		if (!self::isWithin($absolute, $base))
		{
			throw new \RuntimeException('Path is outside the allowed directory');
		}

		$realPath = realpath($absolute);
		$realBase = realpath($base);

		return ltrim(substr($realPath, strlen($realBase)), DIRECTORY_SEPARATOR);
	}
}
