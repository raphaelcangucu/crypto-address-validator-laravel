<?php

// config for CryptoAddressValidator
return [
    /*
    |--------------------------------------------------------------------------
    | Default Currency
    |--------------------------------------------------------------------------
    |
    | The default currency to use when validating addresses if no currency
    | is specified. This can be either a currency symbol (e.g., 'btc') or
    | the full currency name (e.g., 'bitcoin').
    |
    */
    'default_currency' => 'btc',

    /*
    |--------------------------------------------------------------------------
    | Default Network Type
    |--------------------------------------------------------------------------
    |
    | The default network type to use for validation. Options include:
    | 'prod' (mainnet), 'testnet', 'stagenet' (for supported currencies)
    |
    */
    'default_network_type' => 'prod',

    /*
    |--------------------------------------------------------------------------
    | Strict Mode
    |--------------------------------------------------------------------------
    |
    | When enabled, strict mode will enforce additional validation rules
    | such as EIP-55 checksum validation for Ethereum addresses.
    | When disabled, the validator is more permissive.
    |
    */
    'strict_mode' => false,

    /*
    |--------------------------------------------------------------------------
    | Custom Currencies
    |--------------------------------------------------------------------------
    |
    | Register custom currency validators here. This allows you to extend
    | the package with support for additional cryptocurrencies not included
    | in the base library.
    |
    | Each currency definition should include:
    | - name: The full name of the cryptocurrency
    | - symbol: The currency symbol (e.g., 'btc', 'eth')
    | - validator: The validator class (must implement ValidatorInterface)
    | - config: Additional configuration options for the validator
    |
    | Example:
    |
    | 'custom_currencies' => [
    |     [
    |         'name' => 'MyCoin',
    |         'symbol' => 'myc',
    |         'validator' => \App\CryptoValidators\MyCoinValidator::class,
    |         'config' => [
    |             'addressTypes' => ['prod' => ['1a'], 'testnet' => ['2b']],
    |         ]
    |     ],
    | ],
    |
    | Note: Your validator class must implement:
    | Multicoin\AddressValidator\Contracts\ValidatorInterface
    |
    | You can extend existing validators or create your own from scratch.
    | See the package documentation for more details on creating custom validators.
    |
    */
    'custom_currencies' => [
        // Add your custom currency definitions here
    ],
];