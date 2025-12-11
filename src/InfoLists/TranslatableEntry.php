<?php

namespace Codedor\TranslatableTabs\InfoLists;

use Closure;
use Codedor\LocaleCollection\Facades\LocaleCollection;
use Codedor\LocaleCollection\Locale;

class TranslatableEntry
{
    public static function make(
        Closure $schema,
        string $localeCollectionClass = LocaleCollection::class
    ): \Filament\Schemas\Components\Section {
        $tabs = $localeCollectionClass::map(
            fn (Locale $locale) => \Filament\Schemas\Components\Tabs\Tab::make($locale->locale())
                ->schema($schema($locale))
        )->toArray();

        return \Filament\Schemas\Components\Section::make([
            \Filament\Schemas\Components\Tabs::make()
                ->tabs($tabs)
                ->contained(false),
        ]);
    }
}
