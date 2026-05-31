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
 * Encrypted, opaque file reference token, safe to send to the client. Only
 * the server can decrypt (Joomla site secret via Encryptor).
 *
 * Payload (JSON, encrypted):
 *   { path, base, context, expiry }
 */
class FileToken
{
	/**
	 * Default time-to-live in seconds (24 hours).
	 *
	 * @var int
	 */
	const DEFAULT_TTL = 86400;

	/**
	 * @var Encryptor
	 */
	private $encryptor;

	public function __construct()
	{
		$this->encryptor = new Encryptor();
	}

	/**
	 * Issue a token binding $absolutePath to $baseDir, plus optional context.
	 *
	 * @param   string  $absolutePath  The absolute path to the file
	 * @param   string  $baseDir       The allowed base directory (absolute)
	 * @param   array   $context       Optional context metadata (e.g. ['field_id' => 123])
	 * @param   int     $ttl           Time-to-live in seconds
	 *
	 * @return  string  The encrypted token string
	 *
	 * @throws  \RuntimeException  If the path is not within $baseDir
	 */
	public function issue($absolutePath, $baseDir, $context = [], $ttl = self::DEFAULT_TTL)
	{
		$realBase = realpath($baseDir);
		if ($realBase === false || !is_dir($realBase))
		{
			throw new \RuntimeException('Base directory does not exist');
		}

		$relativePath = SafePath::relativeTo($absolutePath, $baseDir);

		// $realBase is encrypted with the rest of the payload — safe to embed.
		$payload = [
			'path'    => $relativePath,
			'base'    => $realBase,
			'context' => $context,
			'expiry'  => time() + $ttl
		];

		return $this->encryptor->encryptJson($payload);
	}

	/**
	 * Resolve a token to its absolute file path. Validates integrity, expiry
	 * and that the path is still inside the embedded base.
	 *
	 * @param   string  $token  The encrypted token string
	 *
	 * @return  string  The resolved absolute file path
	 *
	 * @throws  \RuntimeException  On decryption failure, expiry, or path violation
	 */
	public function read($token)
	{
		$payload = $this->decryptPayload($token);

		$this->validateExpiry($payload);

		$baseDir = $payload['base'];

		if (!is_dir($baseDir))
		{
			throw new \RuntimeException('Token base directory no longer exists');
		}

		return SafePath::absoluteWithin($payload['path'], $baseDir);
	}

	/**
	 * Decrypt the token and return its context array (without resolving the path).
	 *
	 * @param   string  $token  The encrypted token string
	 *
	 * @return  array  The context array
	 *
	 * @throws  \RuntimeException  On decryption failure
	 */
	public function context($token)
	{
		$payload = $this->decryptPayload($token);

		return $payload['context'] ?? [];
	}

	/**
	 * Decrypt and validate the token payload structure.
	 *
	 * @param   string  $token
	 *
	 * @return  array  The decoded payload
	 *
	 * @throws  \RuntimeException
	 */
	private function decryptPayload($token)
	{
		if (empty($token) || !is_string($token))
		{
			throw new \RuntimeException('Invalid token');
		}

		$payload = $this->encryptor->decryptJson($token);

		if (!is_array($payload)
			|| !isset($payload['path'])
			|| !isset($payload['base'])
			|| !isset($payload['expiry']))
		{
			throw new \RuntimeException('Invalid token payload');
		}

		return $payload;
	}

	/**
	 * Validate that the token has not expired.
	 *
	 * @param   array  $payload
	 *
	 * @throws  \RuntimeException
	 */
	private function validateExpiry($payload)
	{
		if (time() > (int) $payload['expiry'])
		{
			throw new \RuntimeException('Token has expired');
		}
	}
}
