<?php

declare(strict_types=1);

namespace Wexample\SymfonyWallet\Tests\Unit;

use Doctrine\DBAL\DriverManager;
use Doctrine\DBAL\Logging\Middleware;
use Doctrine\DBAL\Types\Type;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;
use Doctrine\ORM\Tools\SchemaTool;
use PHPUnit\Framework\TestCase;
use Psr\Log\AbstractLogger;
use Stringable;
use Symfony\Bridge\Doctrine\Types\UuidType;
use Wexample\SymfonyWallet\Service\CredentialTypeRegistry;
use Wexample\SymfonyWallet\Service\WalletFactory;
use Wexample\SymfonyWallet\Tests\Unit\Fixtures\TestCredential;

/**
 * The wallet reads every credential with its parameters in one query, the
 * relation found under whatever name the application gave it.
 */
class WalletFactoryTest extends TestCase
{
    public function testEveryCredentialWithItsParametersInOneQuery(): void
    {
        $queries = new class () extends AbstractLogger {
            public int $count = 0;

            public function log($level, string|Stringable $message, array $context = []): void
            {
                if (isset($context['sql'])) {
                    ++$this->count;
                }
            }
        };

        if (! Type::hasType(UuidType::NAME)) {
            Type::addType(UuidType::NAME, UuidType::class);
        }

        $config = ORMSetup::createAttributeMetadataConfig([__DIR__.'/Fixtures'], true);
        $config->enableNativeLazyObjects(true);
        $config->setMiddlewares([new Middleware($queries)]);
        $entityManager = new EntityManager(DriverManager::getConnection(['driver' => 'pdo_sqlite', 'memory' => true], $config), $config);
        (new SchemaTool($entityManager))->createSchema($entityManager->getMetadataFactory()->getAllMetadata());

        $credentials = [];
        foreach (['stripe' => ['key' => 'sk_1', 'account' => 'acct_1'], 'mailer' => ['dsn' => 'smtp://x'], 'empty' => []] as $type => $parameters) {
            $credential = (new TestCredential())->setTitle($type);
            $credential->setDateCreatedNow();
            $credential->setType($type);
            foreach ($parameters as $name => $value) {
                $credential->setParameterValue($name, $value);
            }
            $entityManager->persist($credential);
            $credentials[(string) $credential->getId()] = $parameters;
        }
        $entityManager->flush();
        $entityManager->clear();
        $queries->count = 0;

        $wallet = (new WalletFactory($entityManager, new CredentialTypeRegistry([]), TestCredential::class))->create();

        $this->assertSame(1, $queries->count);
        $read = array_map(static function ($credential): array {
            $parameters = $credential->parameters;
            ksort($parameters);

            return $parameters;
        }, $wallet->all());
        $credentials = array_map(static function (array $parameters): array {
            ksort($parameters);

            return $parameters;
        }, $credentials);
        ksort($read);
        ksort($credentials);
        $this->assertSame($credentials, $read);
    }
}
