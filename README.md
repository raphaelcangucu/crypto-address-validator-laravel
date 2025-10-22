# Laravel Crypto Address Validator

[![Latest Version on Packagist](https://img.shields.io/packagist/v/raphaelcangucu/laravel-crypto-address-validator.svg?style=flat-square)](https://packagist.org/packages/raphaelcangucu/laravel-crypto-address-validator)
[![Tests](https://img.shields.io/github/actions/workflow/status/raphaelcangucu/crypto-address-validator-laravel/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/raphaelcangucu/crypto-address-validator-laravel/actions/workflows/run-tests.yml)
[![Total Downloads](https://img.shields.io/packagist/dt/raphaelcangucu/laravel-crypto-address-validator.svg?style=flat-square)](https://packagist.org/packages/raphaelcangucu/laravel-crypto-address-validator)

A Laravel package for validating cryptocurrency wallet addresses across multiple blockchains. Built on top of the [multicoin-address-validator](https://github.com/raphaelcangucu/multicoin-address-validator-php) library.

## Features

- **25+ Cryptocurrencies Supported**: Bitcoin, Ethereum, Cardano, Solana, XRP, Litecoin, Bitcoin Cash, and many more
- **Network Support**: Mainnet, testnet, and stagenet validation
- **Laravel Integration**: Service provider, facade, and dependency injection support
- **Flexible Configuration**: Customizable validation rules and options
- **High Performance**: Optimized for speed and accuracy
- **Comprehensive Testing**: 100% test coverage with extensive test suite

## Supported Cryptocurrencies

Bitcoin (BTC), Ethereum (ETH), Cardano (ADA), Solana (SOL), XRP, Litecoin (LTC), Bitcoin Cash (BCH), Monero (XMR), TRON (TRX), Polkadot (DOT), Dogecoin (DOGE), Chainlink (LINK), Polygon (MATIC), Avalanche (AVAX), Cosmos (ATOM), Tezos (XTZ), VeChain (VET), Algorand (ALGO), Hedera (HBAR), NEAR Protocol (NEAR), Filecoin (FIL), The Graph (GRT), Stellar (XLM), EOS, IOTA, and all ERC-20 tokens.

## Installation

You can install the package via composer:

```bash
composer require raphaelcangucu/laravel-crypto-address-validator
```

The package will automatically register its service provider.

You can publish the config file with:

```bash
php artisan vendor:publish --tag="crypto-address-validator-config"
```

This is the contents of the published config file:

```php
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
```

## Usage

### Facade Usage

```php
use CryptoAddressValidator\Facades\CryptoAddressValidator;

// Validate a Bitcoin address
$isValid = CryptoAddressValidator::validate('1BvBMSEYstWetqTFn5Au4m4GFg7xJaNVN2', 'btc');
// Returns: true

// Validate an Ethereum address
$isValid = CryptoAddressValidator::validate('0x742d35Cc6339C4532CE58b5D3Ea8d5A8d6F6395C', 'eth');
// Returns: true

// Validate with options
$isValid = CryptoAddressValidator::validate(
    'tb1qw508d6qejxtdg4y5r3zarvary0c5xw7kxpjzsx',
    'btc',
    ['networkType' => 'testnet']
);
// Returns: true

// Get all supported currencies
$currencies = CryptoAddressValidator::getCurrencies();

// Find a specific currency
$currency = CryptoAddressValidator::findCurrency('btc');
// Returns: ['symbol' => 'btc', 'name' => 'Bitcoin', ...]

// Check if a currency is supported
$isSupported = CryptoAddressValidator::isSupported('btc');
// Returns: true
```

### Dependency Injection

```php
use CryptoAddressValidator\CryptoAddressValidator;

class PaymentController extends Controller
{
    public function validateAddress(CryptoAddressValidator $validator, Request $request)
    {
        $address = $request->input('address');
        $currency = $request->input('currency');
        
        if ($validator->validate($address, $currency)) {
            return response()->json(['valid' => true]);
        }
        
        return response()->json(['valid' => false, 'message' => 'Invalid address']);
    }
}
```

### Validation in Form Requests

```php
use CryptoAddressValidator\Facades\CryptoAddressValidator;
use Illuminate\Foundation\Http\FormRequest;

class PaymentRequest extends FormRequest
{
    public function rules()
    {
        return [
            'address' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    if (!CryptoAddressValidator::validate($value, $this->currency)) {
                        $fail('The '.$attribute.' is not a valid cryptocurrency address.');
                    }
                },
            ],
            'currency' => 'required|string',
        ];
    }
}
```

### Custom Validation Rule

You can create a custom validation rule:

```php
use CryptoAddressValidator\Facades\CryptoAddressValidator;
use Illuminate\Contracts\Validation\Rule;

class CryptoAddress implements Rule
{
    protected $currency;
    protected $options;

    public function __construct($currency, $options = [])
    {
        $this->currency = $currency;
        $this->options = $options;
    }

    public function passes($attribute, $value)
    {
        return CryptoAddressValidator::validate($value, $this->currency, $this->options);
    }

    public function message()
    {
        return 'The :attribute must be a valid ' . strtoupper($this->currency) . ' address.';
    }
}
```

Usage:

```php
$request->validate([
    'btc_address' => ['required', new CryptoAddress('btc')],
    'eth_address' => ['required', new CryptoAddress('eth')],
    'testnet_address' => ['required', new CryptoAddress('btc', ['networkType' => 'testnet'])],
]);
```

## Advanced Usage

### Validation Options

```php
// Validate with specific network
CryptoAddressValidator::validate($address, 'btc', [
    'networkType' => 'testnet' // 'prod', 'testnet', 'stagenet'
]);

// Ethereum with strict checksum validation
CryptoAddressValidator::validate($address, 'eth', [
    'validateChecksum' => true
]);

// Custom validation options
CryptoAddressValidator::validate($address, 'xrp', [
    'validateTag' => true
]);
```

### Getting Currency Information

```php
// Get all supported currencies
$currencies = CryptoAddressValidator::getCurrencies();
/*
Returns array like:
[
    [
        'symbol' => 'btc',
        'name' => 'Bitcoin',
        'networkTypes' => ['prod', 'testnet']
    ],
    // ... more currencies
]
*/

// Find specific currency
$bitcoin = CryptoAddressValidator::findCurrency('bitcoin');
$ethereum = CryptoAddressValidator::findCurrency('eth');

// Check support
$isSupported = CryptoAddressValidator::isSupported('btc'); // true
$isSupported = CryptoAddressValidator::isSupported('unknown'); // false
```

### Access Underlying Validator

```php
// Get the underlying multicoin validator instance
$validator = CryptoAddressValidator::getValidator();

// Use advanced features directly
$result = $validator->validateWithDetails($address, $currency);
```

## Extending with Custom Currencies

You can extend the package to support additional cryptocurrencies not included in the base library by registering custom validators in the configuration file.

### Creating a Custom Validator

First, create a validator class that implements `Multicoin\AddressValidator\Contracts\ValidatorInterface`:

```php
<?php

namespace App\CryptoValidators;

use Multicoin\AddressValidator\Contracts\ValidatorInterface;

class MyCoinValidator implements ValidatorInterface
{
    protected array $config;

    public function __construct(array $config = [])
    {
        $this->config = $config;
    }

    public function isValidAddress(string $address, array $options = []): bool
    {
        // Implement your validation logic here
        // Example: Check address format, length, checksum, etc.

        $networkType = $options['networkType'] ?? 'prod';

        // Basic validation example
        if (strlen($address) < 26 || strlen($address) > 35) {
            return false;
        }

        // Add your specific validation logic
        return preg_match('/^[MY][a-zA-Z0-9]+$/', $address) === 1;
    }

    public function getSupportedNetworks(): array
    {
        return ['prod', 'testnet'];
    }
}
```

**Tip**: You can extend existing validators to reuse validation logic:

```php
<?php

namespace App\CryptoValidators;

use Multicoin\AddressValidator\Validators\BitcoinValidator;

class MyBitcoinForkValidator extends BitcoinValidator
{
    // Inherits all Bitcoin validation logic
    // Override only what's different for your fork
}
```

### Registering Custom Currencies

Publish the config file if you haven't already:

```bash
php artisan vendor:publish --tag="crypto-address-validator-config"
```

Then add your custom currency to `config/crypto-address-validator.php`:

```php
'custom_currencies' => [
    [
        'name' => 'MyCoin',
        'symbol' => 'myc',
        'validator' => \App\CryptoValidators\MyCoinValidator::class,
        'config' => [
            'addressTypes' => ['prod' => ['1a'], 'testnet' => ['2b']],
            // Add any configuration your validator needs
        ]
    ],
    [
        'name' => 'AnotherCoin',
        'symbol' => 'anc',
        'validator' => \App\CryptoValidators\AnotherCoinValidator::class,
        'config' => [
            'bech32Hrp' => ['prod' => ['ac'], 'testnet' => ['tac']],
        ]
    ],
],
```

### Using Custom Currencies

Once registered, your custom currencies work exactly like the built-in ones:

```php
use CryptoAddressValidator\Facades\CryptoAddressValidator;

// Validate your custom currency
$isValid = CryptoAddressValidator::validate('MY1abc...', 'myc');

// Check if custom currency is supported
$isSupported = CryptoAddressValidator::isSupported('myc'); // true

// Get custom currency info
$currency = CryptoAddressValidator::findCurrency('myc');
```

### Custom Validator Interface

Your validator must implement the following interface:

```php
interface ValidatorInterface
{
    /**
     * @param string $address The address to validate
     * @param array $options Validation options (networkType, etc.)
     * @return bool True if valid, false otherwise
     */
    public function isValidAddress(string $address, array $options = []): bool;

    /**
     * @return array Array of supported network types (e.g., ['prod', 'testnet'])
     */
    public function getSupportedNetworks(): array;
}
```

**Available Options**:
- `networkType`: 'prod', 'testnet', or 'stagenet'
- `validateChecksum`: For checksum validation (Ethereum-style)
- Custom options specific to your validator

## Testing

```bash
composer test
```

## Performance

The package is optimized for high performance:

- **Fast validation**: Uses native cryptocurrency validation algorithms
- **No external dependencies**: All validation is done locally
- **Minimal dependencies**: Built on a lightweight foundation
- **Memory efficient**: Low memory footprint even with large validation sets

## Error Handling

The package handles errors gracefully:

```php
try {
    $isValid = CryptoAddressValidator::validate($address, $currency);
} catch (\Exception $e) {
    // Handle validation errors
    Log::error('Address validation failed: ' . $e->getMessage());
    $isValid = false;
}
```

## Security

- **Input validation**: All inputs are properly validated and sanitized
- **No external API calls**: All validation is done locally for security and performance
- **Safe error handling**: Errors don't expose sensitive information

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Raphael Cangucu](https://github.com/raphaelcangucu)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.