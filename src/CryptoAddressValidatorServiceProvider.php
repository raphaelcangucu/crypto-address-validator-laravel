<?php

namespace CryptoAddressValidator;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Multicoin\AddressValidator\CurrencyFactory;
use Multicoin\AddressValidator\WalletAddressValidator;

class CryptoAddressValidatorServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-crypto-address-validator')
            ->hasConfigFile('crypto-address-validator');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(WalletAddressValidator::class, function ($app) {
            $registry = CurrencyFactory::createRegistry();

            // Register custom currencies from config
            $this->registerCustomCurrencies($registry, $app);

            return new WalletAddressValidator($registry);
        });

        $this->app->singleton(CryptoAddressValidator::class, function ($app) {
            return new CryptoAddressValidator(
                $app->make(WalletAddressValidator::class)
            );
        });

        $this->app->alias(CryptoAddressValidator::class, 'crypto-address-validator');
    }

    /**
     * Register custom currencies from configuration
     *
     * @param CurrencyRegistryInterface $registry
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @return void
     */
    protected function registerCustomCurrencies($registry, $app): void
    {
        $customCurrencies = config('crypto-address-validator.custom_currencies', []);

        foreach ($customCurrencies as $definition) {
            // Validate required fields
            if (!isset($definition['name'], $definition['symbol'], $definition['validator'])) {
                throw new \InvalidArgumentException(
                    'Custom currency definition must include: name, symbol, and validator'
                );
            }

            // Validate validator class exists and implements the correct interface
            if (!class_exists($definition['validator'])) {
                throw new \InvalidArgumentException(
                    "Validator class {$definition['validator']} does not exist"
                );
            }

            $validatorClass = $definition['validator'];
            $config = $definition['config'] ?? [];

            // Create validator instance
            $validator = new $validatorClass($config);

            // Create and register currency
            $currency = new \Multicoin\AddressValidator\Currency(
                $definition['name'],
                $definition['symbol'],
                $validator,
                $config
            );

            $registry->register($currency);
        }
    }
}