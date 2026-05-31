<?php

/**
 * @author          Tassos.gr <info@tassos.gr>
 * @link            https://www.tassos.gr
 * @copyright       Copyright © 2026 Tassos All Rights Reserved
 * @license         GNU GPLv3 <http://www.gnu.org/licenses/gpl.html> or later
*/

namespace Tassos\Framework\Security;

defined('_JEXEC') or die('Restricted access');

use Joomla\CMS\Encrypt\Aes;
use Joomla\CMS\Factory;

/**
 * Site-bound encryption using Joomla's site secret.
 * Data encrypted with this class can only be decrypted on the same Joomla installation.
 */
class Encryptor
{
	/**
	 * Encryption key size in bits.
	 *
	 * @var int
	 */
	protected $keySize = 256;

	/**
	 * Encrypts a string.
	 *
	 * @param   string  $data  Data to encrypt
	 *
	 * @return  string  Encrypted data (URL-safe base64 encoded)
	 *
	 * @throws  \RuntimeException
	 */
	public function encrypt($data)
	{
		if (empty($data))
		{
			return '';
		}

		$aes = $this->createAes();
		$encrypted = $aes->encryptString($data, true);
		// encryptString may also return false on failure
		// so we need to throw an exception manually
		if ($encrypted === false)
		{
			throw new \RuntimeException('Encryption failed');
		}

		// Convert to URL-safe base64 (safe for filenames and URLs)
		// Replace + with -, / with _, and remove = padding
		return rtrim(strtr($encrypted, '+/', '-_'), '=');
	}

	/**
	 * Decrypts a string.
	 *
	 * @param   string  $data  Data to decrypt (URL-safe base64 encoded)
	 *
	 * @return  string  Decrypted data
	 *
	 * @throws  \RuntimeException
	 */
	public function decrypt($data)
	{
		if (empty($data))
		{
			return '';
		}

		// Convert from URL-safe base64 back to standard base64
		// Replace - with +, _ with /, and restore = padding
		$data = str_pad(strtr($data, '-_', '+/'), strlen($data) % 4 ? strlen($data) + 4 - strlen($data) % 4 : strlen($data), '=', STR_PAD_RIGHT);

		$aes = $this->createAes();
		$decrypted = $aes->decryptString($data);

		if ($decrypted === false)
		{
			throw new \RuntimeException('Decryption failed');
		}

		return $decrypted;
	}

	/**
	 * Encrypts data and returns it as a JSON string.
	 *
	 * @param   mixed  $data  Data to encrypt (will be JSON encoded)
	 *
	 * @return  string  Encrypted JSON data
	 *
	 * @throws  \RuntimeException
	 */
	public function encryptJson($data)
	{
		$json = json_encode($data);

		if ($json === false)
		{
			throw new \RuntimeException('Failed to JSON encode data: ' . json_last_error_msg());
		}

		return $this->encrypt($json);
	}

	/**
	 * Decrypts a JSON string and returns the decoded data.
	 *
	 * @param   string  $data  Encrypted JSON data
	 *
	 * @return  mixed  Decrypted and JSON decoded data
	 *
	 * @throws  \RuntimeException
	 */
	public function decryptJson($data)
	{
		$decrypted = $this->decrypt($data);
		if (empty($decrypted))
		{
			return null;
		}

		$decoded = json_decode($decrypted, true);

		if ($decoded === null && json_last_error() !== JSON_ERROR_NONE)
		{
			throw new \RuntimeException('Failed to JSON decode data: ' . json_last_error_msg());
		}

		return $decoded;
	}

	/**
	 * Creates a Joomla AES encryption object.
	 *
	 * @return  Aes  AES encryption object
	 *
	 * @throws  \RuntimeException
	 */
	protected function createAes()
	{
		$secret = $this->getSecret();

		if (empty($secret))
		{
			throw new \RuntimeException('Encryption secret is not available');
		}

		return new Aes($secret, $this->keySize);
	}

	/**
	 * Gets the encryption secret. (Joomla site secret)
	 *
	 * @return  string  Encryption secret
	 */
	protected function getSecret()
	{
		return Factory::getApplication()->getConfig()->get('secret');
	}
}
