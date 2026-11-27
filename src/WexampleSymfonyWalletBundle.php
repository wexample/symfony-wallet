<?php

namespace Wexample\SymfonyWallet;

use Wexample\SymfonyHelpers\Class\AbstractBundle;
use Wexample\SymfonyHelpers\Helper\BundleHelper;
use Wexample\SymfonyHelpers\Interface\LoaderBundleInterface;
use Wexample\SymfonySecurity\Service\SecretCipher;
use Wexample\SymfonyWallet\Doctrine\SecretTextType;

class WexampleSymfonyWalletBundle extends AbstractBundle implements LoaderBundleInterface
{
    /** Its JS entity and repository, `@wexample/symfony-wallet`. */
    public static function getLoaderFrontPaths(): array
    {
        return [
            BundleHelper::getBundleCssAlias(static::class) => __DIR__.'/../assets/',
        ];
    }

    /** The parameters' values read and written through the application's cipher. */
    public function boot(): void
    {
        parent::boot();

        if ($this->container?->has(SecretCipher::class)) {
            SecretTextType::setCipher($this->container->get(SecretCipher::class));
        }
    }
}
