<?php

namespace Codedor\TranslatableTabs\InfoLists;

use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\Tabs;
use Filament\Infolists\Components\TextEntry;

class TranslatableEntry
{
    public static function make(
        array $schema = [],
        string $localeCollectionClass = LocaleCollection::class
    ): Section {
        $currentLocale = app()->getLocale();

        $tabs = $localeCollectionClass::map(
            fn (Locale $locale) => Tabs\Tab::make($locale->locale())
                ->schema(function () use ($schema, $locale) {
                    app()->setLocale($locale->locale());
                    return $schema;
                })
        )->toArray();

        app()->setLocale($currentLocale);

        return Section::make([
            Tabs::make()
                ->tabs($tabs)
                ->contained(false),
        ]);
    }
}
