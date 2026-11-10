## The types

```yaml
# config/packages/wexample_symfony_wallet.yaml
wexample_symfony_wallet:
    credential_class: App\Entity\Credential
    types:
        postgres:
            label: PostgreSQL
            icon: ph:bold/database
            parameters:
                - { name: host }
                - { name: port, default: '5432' }
                - { name: user }
                - { name: password, secret: true, required: false }
```

A parameter is required unless it says `required: false`; one with a `default` is never missing. `CredentialTypeRegistry` gives each type as php-wallet's `CredentialType`, its label and its icon:

```php
$registry->get('postgres')?->parameters;  // array<string, CredentialParameter>
$registry->isSecret('postgres', 'password');
$registry->getLabel('postgres');           // PostgreSQL
$registry->getIcon('postgres');            // ph:bold/database
```

## The entities

```php
#[ORM\Entity]
class Credential extends AbstractCredential
{
    #[ORM\OneToMany(targetEntity: CredentialParameter::class, mappedBy: 'credential', cascade: ['persist'], orphanRemoval: true)]
    private Collection $credentialParameters;

    // getCredentialParameters(), addCredentialParameter(), removeCredentialParameter()
    protected function createCredentialParameter(): CredentialParameter
    {
        return new CredentialParameter();
    }
}

#[ORM\Entity]
class CredentialParameter extends AbstractCredentialParameter
{
    #[ORM\ManyToOne(inversedBy: 'credentialParameters')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Credential $credential = null;
}
```

`AbstractCredential` maps the title, the type, the date of creation and the outcome of the last test (`checkStatus`, `checkMessage`, `checkedAt`); `setParameterValue()` adds, sets or removes a parameter, `markCheckPending()` and `forgetCheck()` move the test along.

## The wallet

```php
$wallet = $walletFactory->create();   // php-wallet's Wallet, of every row
$wallet->get((string) $credential->getId())->get('host');
```

Each credential is named by its id and checked against its type when added: a row missing a required parameter fails the wallet.

## What the app tells the bundle

What names a credential is the app's to say: each `CredentialUsageCounterInterface` it implements is summed by `CredentialUsageService`, and a credential named somewhere is not removed.

```php
final readonly class StorageCredentialUsageCounter implements CredentialUsageCounterInterface
{
    public function countUsages(array $credentials): array { /* id => count */ }
}
```

Who opens the connection is the app's too: `CredentialCheckService::ask()` marks the credential pending, saves it and dispatches `CredentialCheckAskedEvent`; the app's listener tests it — or sends it to a worker that does — and writes the outcome (`checkStatus`, `checkMessage`, `checkedAt`). `getOutcome()` reads it back.

`CredentialPresenter` gives a credential in the api's shape of an entity, a secret's value never in it; the JS entity and repository are in `@wexample/symfony-wallet`.
