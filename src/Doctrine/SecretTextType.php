<?php

namespace Wexample\SymfonyWallet\Doctrine;

use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\TextType;
use Wexample\SymfonySecurity\Service\SecretCipher;

/**
 * A text kept encrypted at rest and read in clear (SecretCipher): what a
 * credential's parameter holds. Written encrypted when a key is set; read
 * back whether it was written encrypted or not, a base filling in as its
 * values are saved again. The cipher is the application's, handed in when
 * the bundle boots (WexampleSymfonyWalletBundle::boot()).
 */
final class SecretTextType extends TextType
{
    public const string NAME = 'secret_text';

    private static ?SecretCipher $cipher = null;

    public static function setCipher(SecretCipher $cipher): void
    {
        self::$cipher = $cipher;
    }

    public function convertToDatabaseValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (null === $value || '' === $value) {
            return $value;
        }

        return self::$cipher ? self::$cipher->encrypt((string) $value) : $value;
    }

    public function convertToPHPValue(mixed $value, AbstractPlatform $platform): mixed
    {
        if (null === $value) {
            return null;
        }

        return (self::$cipher ?? new SecretCipher())->decrypt((string) $value);
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function requiresSQLCommentHint(AbstractPlatform $platform): bool
    {
        return true;
    }
}
