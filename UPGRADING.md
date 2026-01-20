# Upgrading

## From v2 to v3

- Install wotz/filament-translatable-tabs instead of codedor/filament-translatable-tabs
- Replace all occurrences of Codedor\TranslatableTabs namespace with new Wotz\TranslatableTabs namespace

## From v1 to v2

### TranslatableEntry

Instead of an array, we expect a closure now.

```php
use Codedor\TranslatableTabs\InfoLists\TranslatableEntry;

TranslatableEntry::make(fn (Locale $locale) => [
    TextEntry::make("description.{$locale->locale()}")
        ->label('Description')
        ->placeholder('-'),
]),
```
