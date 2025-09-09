<?php

namespace RedberryProducts\MdNotion;

use RedberryProducts\MdNotion\Adapters\BlockAdapterFactory;
use RedberryProducts\MdNotion\SDK\Notion;
use RedberryProducts\MdNotion\Services\BlockRegistry;
use RedberryProducts\MdNotion\Services\ContentManager;
use RedberryProducts\MdNotion\Services\DatabaseReader;
use RedberryProducts\MdNotion\Services\DatabaseTable;
use RedberryProducts\MdNotion\Services\PageReader;
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

        $this->app->singleton(BlockRegistry::class, function ($app) {
            return new BlockRegistry(
                $app->make(BlockAdapterFactory::class)
            );
        });

        $this->app->singleton(ContentManager::class, function ($app) {
            return new ContentManager(
                $app->make(Notion::class),
                $app->make(BlockRegistry::class)
            );
        });

        $this->app->singleton(PageReader::class, function ($app) {
            return new PageReader(
                $app->make(Notion::class),
                $app->make(BlockRegistry::class)
            );
        });

        $this->app->singleton(DatabaseReader::class, function ($app) {
            return new DatabaseReader(
                $app->make(Notion::class),
                $app->make(DatabaseTable::class)
            );
        });

        $this->app->singleton(DatabaseTable::class, function ($app) {
            return new DatabaseTable(
                $app->make(Notion::class),
                $app->make(ContentManager::class)
            );
        });
    }
}
