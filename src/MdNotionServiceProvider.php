<?php

namespace RedberryProducts\MdNotion;

use RedberryProducts\MdNotion\SDK\Notion;
use RedberryProducts\MdNotion\Adapters\BlockAdapterFactory;
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
            ->hasConfigFile()
            ->hasViews();
        // ->hasCommand(MdNotionCommand::class);
    }

    public function packageRegistered(): void
    {
        $this->app->singleton(Notion::class, function ($app) {
            $config = $app['config']['md-notion'];
            return new Notion(
                $config['notion_api_key'],
                '2025-09-03'
            );
        });

        $this->app->singleton(BlockAdapterFactory::class, function ($app) {
            $config = $app['config']['md-notion'];
            return new BlockAdapterFactory(
                $app->make(Notion::class),
                $config['adapters'] ?? []
            );
        });
    }
}
