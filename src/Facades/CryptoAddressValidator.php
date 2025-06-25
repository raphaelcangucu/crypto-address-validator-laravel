<?php

namespace CryptoAddressValidator\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \CryptoAddressValidator\CryptoAddressValidator
 *
 * @method static bool validate(string $address, ?string $currency = null, array $options = [])
 * @method static array getCurrencies()
 * @method static array|null findCurrency(string $nameOrSymbol)
 * @method static bool isSupported(string $nameOrSymbol)
 * @method static \Multicoin\AddressValidator\WalletAddressValidator getValidator()
 */
class CryptoAddressValidator extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \CryptoAddressValidator\CryptoAddressValidator::class;
    }
}