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
        $this->app->singleton(WalletAddressValidator::class, function () {
            $registry = CurrencyFactory::createRegistry();
            return new WalletAddressValidator($registry);
        });

        $this->app->singleton(CryptoAddressValidator::class, function ($app) {
            return new CryptoAddressValidator(
                $app->make(WalletAddressValidator::class)
            );
        });

        $this->app->alias(CryptoAddressValidator::class, 'crypto-address-validator');
    }
}