<?php

namespace RedberryProducts\MdNotion;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class MdNotionServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('md-notion')
            ->hasConfigFile();
        // ->hasCommand(MdNotionCommand::class);
    }
}
