<?php
/**
 * Akeeba WebPush
 *
 * An abstraction layer for easier implementation of WebPush in Joomla components.
 *
 * @copyright Copyright (c) 2022-2024 Akeeba Ltd
 * @license   GNU GPL v3 or later; see LICENSE.txt
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 */

/** @noinspection PhpIllegalPsrClassPathInspection */

namespace Lcobucci\JWT\Signer;

use InvalidArgumentException;
use function is_resource;
use function openssl_error_string;
use function openssl_free_key;
use function openssl_pkey_get_details;
use function openssl_pkey_get_private;
use function openssl_pkey_get_public;
use function openssl_sign;
use function openssl_verify;

abstract class OpenSSL extends BaseSigner
{
    public function createHash($payload, Key $key)
    {
        $privateKey = $this->getPrivateKey($key->getContent(), $key->getPassphrase());

        try {
            $signature = '';

            if (! openssl_sign($payload, $signature, $privateKey, $this->getAlgorithm())) {
                throw CannotSignPayload::errorHappened(openssl_error_string());
            }

            return $signature;
        } finally {
            @openssl_free_key($privateKey);
        }
    }

    /**
     * @param string $pem
     * @param string $passphrase
     *
     * @return resource
     */
    private function getPrivateKey($pem, $passphrase)
    {
        $privateKey = openssl_pkey_get_private($pem, $passphrase);
        $this->validateKey($privateKey);

        return $privateKey;
    }

    /**
     * @param $expected
     * @param $payload
     * @param $key
     * @return bool
     */
    public function doVerify($expected, $payload, Key $key)
    {
        $publicKey = $this->getPublicKey($key->getContent());
        $result    = openssl_verify($payload, $expected, $publicKey, $this->getAlgorithm());
        openssl_free_key($publicKey);

        return $result === 1;
    }

    /**
     * @param string $pem
     *
     * @return resource
     */
    private function getPublicKey($pem)
    {
        $publicKey = openssl_pkey_get_public($pem);
        $this->validateKey($publicKey);

        return $publicKey;
    }

    /**
     * Raises an exception when the key type is not the expected type
     *
     * @param resource|bool $key
     *
     * @throws InvalidArgumentException
     */
    private function validateKey($key)
    {
        if (! is_resource($key) && !is_object($key)) {
            throw InvalidKeyProvided::cannotBeParsed(openssl_error_string());
        }

        $details = openssl_pkey_get_details($key);

        if (! isset($details['key']) || $details['type'] !== $this->getKeyType()) {
            throw InvalidKeyProvided::incompatibleKey();
        }
    }

    /**
     * Returns the type of key to be used to create/verify the signature (using OpenSSL constants)
     *
     * @internal
     */
    abstract public function getKeyType();

    /**
     * Returns which algorithm to be used to create/verify the signature (using OpenSSL constants)
     *
     * @internal
     */
    abstract public function getAlgorithm();
}
