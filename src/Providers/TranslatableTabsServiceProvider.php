<?php

namespace Codedor\TranslatableTabs\Providers;

use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class TranslatableTabsServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('filament-translatable-tabs')
            ->hasViews()
            ->setBasePath(__DIR__ . '/../')
            ->hasTranslations();
    }
}
