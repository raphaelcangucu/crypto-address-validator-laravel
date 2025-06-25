<?php

namespace CryptoAddressValidator;

use Multicoin\AddressValidator\WalletAddressValidator;

class CryptoAddressValidator
{
    public function __construct(
        protected WalletAddressValidator $validator
    ) {}

    /**
     * Validate a cryptocurrency address
     *
     * @param string $address The address to validate
     * @param string|null $currency Currency symbol or name (defaults to 'bitcoin')
     * @param array $options Additional validation options
     * @return bool
     */
    public function validate(string $address, ?string $currency = null, array $options = []): bool
    {
        return $this->validator->validate($address, $currency, $options);
    }

    /**
     * Get all supported currencies
     *
     * @return array
     */
    public function getCurrencies(): array
    {
        return $this->validator->getCurrencies();
    }

    /**
     * Find a currency by name or symbol
     *
     * @param string $nameOrSymbol
     * @return array|null
     */
    public function findCurrency(string $nameOrSymbol): ?array
    {
        return $this->validator->findCurrency($nameOrSymbol);
    }

    /**
     * Check if a currency is supported
     *
     * @param string $nameOrSymbol
     * @return bool
     */
    public function isSupported(string $nameOrSymbol): bool
    {
        return $this->validator->isSupported($nameOrSymbol);
    }

    /**
     * Get the underlying validator instance
     *
     * @return WalletAddressValidator
     */
    public function getValidator(): WalletAddressValidator
    {
        return $this->validator;
    }
}