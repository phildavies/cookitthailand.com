<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2026 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace Tassos\Framework;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;
use Joomla\Filesystem\Path;
use Joomla\Registry\Registry;
use Tassos\Framework\File;
use Tassos\Framework\Security\FileReference;
use Tassos\Framework\Security\FileToken;
use Tassos\Framework\Security\SafePath;

/**
 * Service/facade over the file-upload security primitives (FileToken / FileReference)
 * for upload and delete AJAX flows.
 */
class FileUpload
{
	/**
	 * Persist $_FILES['file'] to disk
	 *
	 * Shared by handleUpload() and the framework's gallery widgets. 
	 * 
	 * Registry keys:
	 *  - upload_folder   (string|null) Absolute path; null => File::getTempFolder()
	 *  - upload_types    (string)      Forwarded to File::upload (default '*')
	 *  - allow_unsafe    (bool)        Selects 'raw' vs 'cmd' input filter and is
	 *                                  forwarded to File::upload
	 *  - filename_prefix (string|null) Forwarded as $random_prefix
	 *  - filename_suffix (bool)        Forwarded as $random_suffix (default false)
	 *
	 * @param   Registry  $options
	 *
	 * @return  string  Absolute path of the uploaded file.
	 *
	 * @throws  \Exception  On request-shape or upload failure.
	 */
	public static function uploadFromRequest(Registry $options)
	{
		$input = Factory::getApplication()->input;

		$allow_unsafe = (bool) $options->get('allow_unsafe', false);

		$file = $input->files->get('file', null, $allow_unsafe ? 'raw' : 'cmd');
		if (!$file)
		{
			throw new \RuntimeException('NR_FILE_UPLOAD_INVALID_FILE');
		}

		// Multi-upload payloads arrive as a 2-level array.
		$first = array_pop($file);
		if (is_array($first))
		{
			$file = $first;
		}

		$upload_folder   = $options->get('upload_folder', null);
		$allowed_types   = $options->get('upload_types', '*');
		$filename_prefix = $options->get('filename_prefix', null);
		$filename_suffix = (bool) $options->get('filename_suffix', false);

		return File::upload($file, $upload_folder, $allowed_types, $allow_unsafe, $filename_prefix, $filename_suffix);
	}

	/**
	 * Issue a token for the given absolute path and emit the upload response, then exit.
	 *
	 * @param   string  $absolutePath  Absolute path of the persisted file.
	 * @param   string  $base          Token base directory.
	 * @param   array   $context       Token context for cross-request binding.
	 * @param   bool    $exposeUrl     Include the public URL when under JPATH_ROOT.
	 * @param   bool    $emitLegacy    Include the legacy `file` and `file_encode`
	 *                                 base64-path keys. Defaults to true so older
	 *                                 Dropzone clients keep working. Migrated
	 *                                 callers should pass false to get a
	 *                                 token-only response.
	 *
	 * @return  void  Always exits.
	 */
	public static function emitUploadResponse($absolutePath, $base, array $context = [], $exposeUrl = true, $emitLegacy = true)
	{
		$token = '';
		try
		{
			$token = (new FileToken())->issue($absolutePath, $base, $context);
		}
		catch (\Throwable $e) {}

		$response = ['file_token' => $token];

		// Back-compat: only emitted for clients whose JS doesn't read file_token.
		if ($emitLegacy)
		{
			$response['file']        = base64_encode($absolutePath);
			$response['file_encode'] = base64_encode(str_replace([JPATH_SITE, JPATH_ROOT], '', $absolutePath));
		}

		if ($exposeUrl)
		{
			if ($url = self::computePublicUrl($absolutePath))
			{
				$response['url'] = $url;
			}
		}

		header('Content-Type: application/json');
		echo json_encode($response);
		Factory::getApplication()->close();
	}

	/**
	 * Handle an upload request end-to-end.
	 *
	 * Composes uploadFromRequest() and emitUploadResponse().
	 *
	 * Registry keys:
	 *  - upload_folder   (string|null) Absolute path; null => File::getTempFolder()
	 *  - upload_types    (string)      Forwarded to File::upload (default '*')
	 *  - allow_unsafe    (bool)        Selects 'raw' vs 'cmd' input filter and is
	 *                                  forwarded to File::upload
	 *  - filename_prefix (string|null) Forwarded as $random_prefix
	 *  - filename_suffix (bool)        Forwarded as $random_suffix (default false)
	 *  - token_context  (array)  		Stored inside the token for cross-request binding
	 *  - expose_url     (bool)   		Include a public URL when under JPATH_ROOT (default true)
	 *  - emit_legacy    (bool)   		Include the legacy `file`/`file_encode` keys in
	 *                            		the response (default true). Migrated callers
	 *                            		should set this to false for a token-only payload.
	 *
	 * @param   Registry  $options
	 *
	 * @return  void  Always exits.
	 */
	public static function handleUpload(Registry $options)
	{
		try
		{
			$uploaded = self::uploadFromRequest($options);

			$upload_folder = $options->get('upload_folder', null);
			$base = is_null($upload_folder) ? File::getTempFolder() : $upload_folder;

			self::emitUploadResponse(
				$uploaded,
				$base,
				(array) $options->get('token_context', []),
				(bool) $options->get('expose_url', true),
				(bool) $options->get('emit_legacy', true)
			);
		}
		catch (\Throwable $th)
		{
			self::uploadDie($th->getMessage());
		}
	}

	/**
	 * Handle a delete request end-to-end.
	 *
	 * Reads file_token / filename from input, optionally validates the token's
	 * embedded context against the supplied expectation, resolves the reference
	 * via FileReference, deletes the file and emits {"success": true}.
	 *
	 * The `filename` input is the LEGACY field for clients that still send a
	 * base64-encoded path. Newer clients send `file_token` (which takes
	 * precedence here).
	 *
	 * Registry keys:
	 *  - allowed_bases     (string[])  Required. Absolute paths.
	 *  - expected_context  (array)     Optional. Every key/value must match the token's context.
	 *
	 * @param   Registry  $options
	 *
	 * @return  void  Always exits.
	 */
	public static function handleDelete(Registry $options)
	{
		$input = Factory::getApplication()->input;

		$file_token = $input->getString('file_token', '');
		$filename   = $input->getString('filename', '');

		$reference = $file_token ?: $filename;
		if (!$reference)
		{
			self::uploadDie('NR_FILE_UPLOAD_INVALID_FILE');
		}

		$allowed_bases = (array) $options->get('allowed_bases', []);
		if (empty($allowed_bases))
		{
			self::uploadDie('NR_FILE_UPLOAD_INVALID_FILE');
		}

		try
		{
			$expected = (array) $options->get('expected_context', []);
			if ($file_token && !empty($expected))
			{
				self::assertContextMatches($file_token, $expected);
			}

			try
			{
				$absolutePath = FileReference::resolve($reference, $allowed_bases);
			}
			catch (\RuntimeException $e)
			{
				// Idempotent-delete fallback: The referenced file may have been
				// moved out of an allowed base by a concurrent operation — most
				// commonly an old CF form whose successful submit moves the
				// upload to its destination, then the client cleans up via this
				// endpoint. FileReference::resolve fails in that case because
				// realpath() can't validate a non-existent path. If the
				// reference still lands structurally inside an allowed base,
				// treat the delete as a successful no-op.
				if (!self::referenceLandsInsideBase($reference, $allowed_bases))
				{
					throw $e;
				}

				header('Content-Type: application/json');
				echo json_encode(['success' => true]);
				Factory::getApplication()->close();
				return;
			}

			if (is_file($absolutePath))
			{
				File::delete($absolutePath);
			}

			header('Content-Type: application/json');
			echo json_encode(['success' => true]);
			Factory::getApplication()->close();
		}
		catch (\Throwable $th)
		{
			self::uploadDie($th->getMessage());
		}
	}

	/**
	 * Structural-only check: does the legacy-decoded reference resolve to a
	 * candidate path that sits inside one of the allowed bases? Used by
	 * handleDelete() to make idempotent-delete cleanups succeed when the
	 * underlying file has already been moved away by a concurrent operation.
	 *
	 * @param   string  $reference
	 * @param   array   $allowedBases
	 *
	 * @return  bool
	 */
	private static function referenceLandsInsideBase($reference, array $allowedBases)
	{
		if (!is_string($reference) || $reference === '')
		{
			return false;
		}

		$reference = trim($reference);

		// Mirror FileReference::decodeLegacyInput — only accept the decoded
		// value when it round-trips back to the input.
		$decoded = base64_decode($reference, true);
		if ($decoded !== false && $decoded !== $reference && base64_encode($decoded) === $reference)
		{
			$path = $decoded;
		}
		else
		{
			$path = $reference;
		}

		if (strpos($path, "\0") !== false
			|| preg_match('#(?:^|[\\\\/])\.\.(?:[\\\\/]|$)#', $path))
		{
			return false;
		}

		$candidates = [];
		if (SafePath::isAbsolute($path))
		{
			$candidates[] = Path::clean($path);
		}
		// JPATH_ROOT-relative fallback covers older extension versions that strip JPATH_ROOT,
		// producing values like "/tmp/file.png" that are really JPATH_ROOT . "/tmp/file.png".
		$candidates[] = Path::clean(JPATH_ROOT . DIRECTORY_SEPARATOR . ltrim($path, '/\\'));

		foreach ($allowedBases as $base)
		{
			$cleanBase = rtrim(Path::clean($base), DIRECTORY_SEPARATOR);
			if ($cleanBase === '')
			{
				continue;
			}
			$prefix = $cleanBase . DIRECTORY_SEPARATOR;

			foreach ($candidates as $candidate)
			{
				if ($candidate === $cleanBase || strpos($candidate, $prefix) === 0)
				{
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * Resolve a client-submitted reference (token or legacy path) to a
	 * validated absolute path.
	 *
	 * NOTE: $allowedBases is enforced ONLY for the legacy path-resolution
	 * branch. A valid FileToken returns its embedded path without consulting
	 * $allowedBases — token authority is self-contained.
	 *
	 * @param   string  $reference
	 * @param   array   $allowedBases
	 *
	 * @return  string
	 */
	public static function resolveReference($reference, array $allowedBases)
	{
		return FileReference::resolve($reference, $allowedBases);
	}

	/**
	 * Resolve a client-submitted reference and, when it is a FileToken,
	 * verify its embedded context contains every key/value in $expected.
	 *
	 * Legacy plaintext/base64 paths skip the context check (there is no
	 * context attached to them) and are gated only by $allowedBases.
	 *
	 * @param   string  $reference
	 * @param   array   $allowedBases
	 * @param   array   $expected      Required context keys/values for tokens
	 *
	 * @return  string  The resolved absolute file path
	 *
	 * @throws  \RuntimeException  When the token's context contradicts $expected,
	 *                             or the reference cannot be resolved safely.
	 */
	public static function resolveReferenceWithContext($reference, array $allowedBases, array $expected = [])
	{
		if (!empty($expected) && self::looksLikeToken($reference))
		{
			self::assertContextMatches($reference, $expected);
		}

		return FileReference::resolve($reference, $allowedBases);
	}

	/**
	 * Cheap heuristic — true if $reference decrypts as a FileToken payload.
	 *
	 * @param   string  $reference
	 *
	 * @return  bool
	 */
	private static function looksLikeToken($reference)
	{
		try
		{
			(new FileToken())->context($reference);
			return true;
		}
		catch (\Throwable $e)
		{
			return false;
		}
	}

	/**
	 * Issue an encrypted token for an already-persisted file.
	 *
	 * @param   string  $absolutePath
	 * @param   string  $baseDir
	 * @param   array   $context
	 * @param   int     $ttl
	 *
	 * @return  string
	 */
	public static function issueToken($absolutePath, $baseDir, array $context = [], $ttl = FileToken::DEFAULT_TTL)
	{
		return (new FileToken())->issue($absolutePath, $baseDir, $context, $ttl);
	}

	/**
	 * Verify that every key/value in $expected appears in the token's stored
	 * context. Throws when the token cannot be decrypted or when the context
	 * does not match.
	 *
	 * @param   string  $token
	 * @param   array   $expected
	 *
	 * @return  void
	 *
	 * @throws  \RuntimeException  When the token cannot be decrypted, or when
	 *                             its context contradicts $expected.
	 */
	private static function assertContextMatches($token, array $expected)
	{
		try
		{
			$context = (new FileToken())->context($token);
		}
		catch (\Throwable $e)
		{
			throw new \RuntimeException('Token context unverifiable');
		}

		foreach ($expected as $key => $value)
		{
			if (!array_key_exists($key, $context) || (string) $context[$key] !== (string) $value)
			{
				throw new \RuntimeException('Token context mismatch');
			}
		}
	}

	/**
	 * Build a browser-reachable URL for a file stored under JPATH_ROOT.
	 *
	 * Used by emitUploadResponse() to populate the `url` field for clients
	 * (e.g. Dropzone-based fields) that render an inline preview straight
	 * after upload, before any token round-trip.
	 *
	 * Returns an empty string when $absolutePath does not resolve under
	 * JPATH_ROOT — this is a deliberate guard so off-root temp locations
	 * are never advertised to the browser.
	 *
	 * Path segments are not URL-encoded; callers must ensure stored
	 * filenames are already web-safe (the upload pipeline sanitises them).
	 *
	 * @param   string  $absolutePath
	 *
	 * @return  string  Absolute URL, or '' if the file is outside the web root.
	 */
	private static function computePublicUrl($absolutePath)
	{
		$root = Path::clean(JPATH_ROOT);
		$path = Path::clean($absolutePath);

		if (strpos($path, $root) !== 0)
		{
			return '';
		}

		$relative = ltrim(substr($path, strlen($root)), '/\\');
		$relative = str_replace(DIRECTORY_SEPARATOR, '/', $relative);

		return rtrim(Uri::root(), '/') . '/' . $relative;
	}

	/**
	 * Emit a 500 response with a translated message and stop execution.
	 *
	 * @param   string  $message
	 *
	 * @return  void
	 */
	private static function uploadDie($message)
	{
		http_response_code(500);
		die(Text::_($message));
	}
}
