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
];