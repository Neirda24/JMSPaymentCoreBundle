<?php

namespace JMS\Payment\CoreBundle\Cryptography;

use Defuse\Crypto\Crypto;
use Defuse\Crypto\Key;

class DefusePhpEncryptionService implements EncryptionServiceInterface
{
    private Key $key;

    public function __construct(string $secret)
    {
        $this->key = Key::loadFromAsciiSafeString($secret);
    }

    public function decrypt(string $encryptedValue): string
    {
        return Crypto::decrypt($encryptedValue, $this->key);
    }

    public function encrypt(string $rawValue): string
    {
        return Crypto::encrypt($rawValue, $this->key);
    }
}
