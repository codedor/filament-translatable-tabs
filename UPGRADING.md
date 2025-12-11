# Upgrading

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
