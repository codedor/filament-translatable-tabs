<?php

namespace Codedor\TranslatableTabs\InfoLists;

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;

class TranslatableEntry
{
    public static function make(
        array $schema = [],
        string $localeCollectionClass = LocaleCollection::class
    ): \Filament\Schemas\Components\Section {
        $currentLocale = app()->getLocale();

        $tabs = $localeCollectionClass::map(
            fn (Locale $locale) => \Filament\Schemas\Components\Tabs\Tab::make($locale->locale())
                ->schema(function () use ($schema, $locale) {
                    app()->setLocale($locale->locale());

                    return $schema;
                })
        )->toArray();

        app()->setLocale($currentLocale);

        return \Filament\Schemas\Components\Section::make([
            \Filament\Schemas\Components\Tabs::make()
                ->tabs($tabs)
                ->contained(false),
        ]);
    }
}
