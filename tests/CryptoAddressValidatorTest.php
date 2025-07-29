<?php

use CryptoAddressValidator\Facades\CryptoAddressValidator;

it('can validate bitcoin addresses', function () {
    $valid = CryptoAddressValidator::validate('1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2', 'btc');
    expect($valid)->toBeTrue();
});

it('can validate ethereum addresses', function () {
    $valid = CryptoAddressValidator::validate('0x742d35Cc6339C4532CE58b5D3Ea8d5A8d6F6395C', 'eth');
    expect($valid)->toBeTrue();
});

it('can validate solana addresses', function () {
    $valid = CryptoAddressValidator::validate('HN7cABqLq46Es1jh92dQQi5kHMKvH4EeMEJQo4AmwPPJ', 'sol');
    expect($valid)->toBeTrue();
});

it('can validate TON addresses', function () {
    $valid = CryptoAddressValidator::validate('EQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAM9c', 'ton');
    expect($valid)->toBeTrue();
});

it('can validate TON addresses with memo', function () {
    $valid = CryptoAddressValidator::validate(
        'EQAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAM9c',
        'ton',
        ['memo' => '123456789']
    );
    expect($valid)->toBeTrue();
});

it('rejects invalid addresses', function () {
    $valid = CryptoAddressValidator::validate('invalid-address', 'btc');
    expect($valid)->toBeFalse();
});

it('can get supported currencies', function () {
    $currencies = CryptoAddressValidator::getCurrencies();
    expect($currencies)->toBeArray()
        ->and(count($currencies))->toBeGreaterThan(0);
});

it('can find specific currency', function () {
    $currency = CryptoAddressValidator::findCurrency('btc');
    expect($currency)->toBeArray()
        ->and($currency['symbol'])->toBe('btc')
        ->and($currency['name'])->toBe('Bitcoin');
});

it('can check if currency is supported', function () {
    expect(CryptoAddressValidator::isSupported('btc'))->toBeTrue();
    expect(CryptoAddressValidator::isSupported('unknown-coin'))->toBeFalse();
});

it('can validate with network options', function () {
    $valid = CryptoAddressValidator::validate(
        'tb1qw508d6qejxtdg4y5r3zarvary0c5xw7kxpjzsx',
        'btc',
        ['networkType' => 'testnet']
    );
    expect($valid)->toBeTrue();
});

it('can access underlying validator', function () {
    $validator = CryptoAddressValidator::getValidator();
    expect($validator)->toBeInstanceOf(\Multicoin\AddressValidator\WalletAddressValidator::class);
});