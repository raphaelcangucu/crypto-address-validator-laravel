<?php

use CryptoAddressValidator\Facades\CryptoAddressValidator;
use Multicoin\AddressValidator\Contracts\ValidatorInterface;

// Mock custom validator for testing
class MockCoinValidator implements ValidatorInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function isValidAddress(string $address, array $options = []): bool
    {
        // Simple validation: address must start with 'MOCK' and be 40 characters
        return str_starts_with($address, 'MOCK') && strlen($address) === 40;
    }

    public function getSupportedNetworks(): array
    {
        return ['prod', 'testnet'];
    }
}

// Another mock validator extending Bitcoin validator
class MockBitcoinForkValidator extends \Multicoin\AddressValidator\Validators\BitcoinValidator
{
    // Inherits Bitcoin validation logic
}

beforeEach(function () {
    // Reset config before each test
    config(['crypto-address-validator.custom_currencies' => []]);
});

it('can register a custom currency', function () {
    config([
        'crypto-address-validator.custom_currencies' => [
            [
                'name' => 'MockCoin',
                'symbol' => 'mock',
                'validator' => MockCoinValidator::class,
                'config' => [],
            ],
        ],
    ]);

    // Force re-binding to pick up new config
    app()->forgetInstance(\Multicoin\AddressValidator\WalletAddressValidator::class);
    app()->forgetInstance(\CryptoAddressValidator\CryptoAddressValidator::class);

    expect(CryptoAddressValidator::isSupported('mock'))->toBeTrue();
});

it('can validate custom currency addresses', function () {
    config([
        'crypto-address-validator.custom_currencies' => [
            [
                'name' => 'MockCoin',
                'symbol' => 'mock',
                'validator' => MockCoinValidator::class,
                'config' => [],
            ],
        ],
    ]);

    // Force re-binding
    app()->forgetInstance(\Multicoin\AddressValidator\WalletAddressValidator::class);
    app()->forgetInstance(\CryptoAddressValidator\CryptoAddressValidator::class);

    // Valid address (starts with MOCK and is 40 chars)
    $validAddress = 'MOCK' . str_repeat('1', 36);
    expect(CryptoAddressValidator::validate($validAddress, 'mock'))->toBeTrue();

    // Invalid address (doesn't start with MOCK)
    expect(CryptoAddressValidator::validate('INVALID123456789012345678901234567890', 'mock'))->toBeFalse();

    // Invalid address (wrong length)
    expect(CryptoAddressValidator::validate('MOCK123', 'mock'))->toBeFalse();
});

it('can register multiple custom currencies', function () {
    config([
        'crypto-address-validator.custom_currencies' => [
            [
                'name' => 'MockCoin',
                'symbol' => 'mock',
                'validator' => MockCoinValidator::class,
                'config' => [],
            ],
            [
                'name' => 'MockCoin2',
                'symbol' => 'mock2',
                'validator' => MockCoinValidator::class,
                'config' => [],
            ],
        ],
    ]);

    // Force re-binding
    app()->forgetInstance(\Multicoin\AddressValidator\WalletAddressValidator::class);
    app()->forgetInstance(\CryptoAddressValidator\CryptoAddressValidator::class);

    expect(CryptoAddressValidator::isSupported('mock'))->toBeTrue();
    expect(CryptoAddressValidator::isSupported('mock2'))->toBeTrue();
});

it('can find custom currency by symbol', function () {
    config([
        'crypto-address-validator.custom_currencies' => [
            [
                'name' => 'MockCoin',
                'symbol' => 'mock',
                'validator' => MockCoinValidator::class,
                'config' => ['testConfig' => 'value'],
            ],
        ],
    ]);

    // Force re-binding
    app()->forgetInstance(\Multicoin\AddressValidator\WalletAddressValidator::class);
    app()->forgetInstance(\CryptoAddressValidator\CryptoAddressValidator::class);

    $currency = CryptoAddressValidator::findCurrency('mock');

    expect($currency)->toBeArray()
        ->and($currency['symbol'])->toBe('mock')
        ->and($currency['name'])->toBe('MockCoin');
});

it('throws exception when validator class is missing', function () {
    config([
        'crypto-address-validator.custom_currencies' => [
            [
                'name' => 'InvalidCoin',
                'symbol' => 'inv',
                // Missing validator class
                'config' => [],
            ],
        ],
    ]);

    // Force re-binding should throw exception
    app()->forgetInstance(\Multicoin\AddressValidator\WalletAddressValidator::class);
    app()->forgetInstance(\CryptoAddressValidator\CryptoAddressValidator::class);

    // This should throw an exception when trying to instantiate
    expect(fn() => CryptoAddressValidator::isSupported('inv'))
        ->toThrow(\InvalidArgumentException::class);
});

it('throws exception when validator class does not exist', function () {
    config([
        'crypto-address-validator.custom_currencies' => [
            [
                'name' => 'InvalidCoin',
                'symbol' => 'inv',
                'validator' => 'NonExistentValidatorClass',
                'config' => [],
            ],
        ],
    ]);

    // Force re-binding should throw exception
    app()->forgetInstance(\Multicoin\AddressValidator\WalletAddressValidator::class);
    app()->forgetInstance(\CryptoAddressValidator\CryptoAddressValidator::class);

    expect(fn() => CryptoAddressValidator::isSupported('inv'))
        ->toThrow(\InvalidArgumentException::class, 'does not exist');
});

it('can extend existing validators', function () {
    config([
        'crypto-address-validator.custom_currencies' => [
            [
                'name' => 'BitcoinFork',
                'symbol' => 'btcf',
                'validator' => MockBitcoinForkValidator::class,
                'config' => [
                    'addressTypes' => ['prod' => ['00', '05'], 'testnet' => ['6f', 'c4']],
                    'bech32Hrp' => ['prod' => ['bc'], 'testnet' => ['tb']],
                ],
            ],
        ],
    ]);

    // Force re-binding
    app()->forgetInstance(\Multicoin\AddressValidator\WalletAddressValidator::class);
    app()->forgetInstance(\CryptoAddressValidator\CryptoAddressValidator::class);

    expect(CryptoAddressValidator::isSupported('btcf'))->toBeTrue();

    // Should validate Bitcoin addresses since it extends BitcoinValidator
    expect(CryptoAddressValidator::validate('1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2', 'btcf'))->toBeTrue();
});

it('custom currencies work alongside built-in currencies', function () {
    config([
        'crypto-address-validator.custom_currencies' => [
            [
                'name' => 'MockCoin',
                'symbol' => 'mock',
                'validator' => MockCoinValidator::class,
                'config' => [],
            ],
        ],
    ]);

    // Force re-binding
    app()->forgetInstance(\Multicoin\AddressValidator\WalletAddressValidator::class);
    app()->forgetInstance(\CryptoAddressValidator\CryptoAddressValidator::class);

    // Built-in currencies should still work
    expect(CryptoAddressValidator::validate('1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2', 'btc'))->toBeTrue();
    expect(CryptoAddressValidator::validate('0x742d35Cc6339C4532CE58b5D3Ea8d5A8d6F6395C', 'eth'))->toBeTrue();

    // Custom currency should also work
    $validAddress = 'MOCK' . str_repeat('1', 36);
    expect(CryptoAddressValidator::validate($validAddress, 'mock'))->toBeTrue();
});

it('passes config to custom validator', function () {
    $testConfig = ['testKey' => 'testValue', 'anotherKey' => 123];

    config([
        'crypto-address-validator.custom_currencies' => [
            [
                'name' => 'ConfigTestCoin',
                'symbol' => 'ctc',
                'validator' => MockCoinValidator::class,
                'config' => $testConfig,
            ],
        ],
    ]);

    // Force re-binding
    app()->forgetInstance(\Multicoin\AddressValidator\WalletAddressValidator::class);
    app()->forgetInstance(\CryptoAddressValidator\CryptoAddressValidator::class);

    $currency = CryptoAddressValidator::findCurrency('ctc');

    // Currency should be registered successfully
    expect($currency)->toBeArray()
        ->and($currency['name'])->toBe('ConfigTestCoin')
        ->and($currency['symbol'])->toBe('ctc');
});
