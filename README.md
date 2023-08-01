# Translatable Tabs for Filament

Adds a tab per locale and adds integration for [spatie/laravel-translatable](https://spatie.be/docs/laravel-translatable/v6/introduction) in Filament.

## Installation

You can install the package via composer:

```bash
composer require codedor/filament-translatable-tabs
```

In an effort to align with Filament's theming methodology you will need to use a custom theme to use this plugin.

> **Note**
> If you have not set up a custom theme and are using a Panel follow the instructions in the [Filament Docs](https://filamentphp.com/docs/3.x/panels/themes#creating-a-custom-theme) first. The following applies to both the Panels Package and the standalone Forms package.

1. Import the plugin's stylesheet (if not already included) into your theme's css file.

```css
@import '../../../../vendor/codedor/filament-translatable-tabs/resources/css/plugin.css';
```

2. Add the plugin's views to your `tailwind.config.js` file.

```js
content: [
    ...
    './vendor/codedor/filament-translatable-tabs/resources/**/*.blade.php',
]
```

## Usage

```php
use Codedor\TranslatableTabs\Forms\TranslatableTabs;

public static function form(Form $form): Form
{
    return $form->schema([
        TranslatableTabs::make('Translations')
            ->defaultFields([
                TextInput::make('working_title')
                    ->required()
                    ->maxLength(255),
            ])
            ->translatableFields([
                TextInput::make("title")
                    ->label('Title')
                    ->required(fn (Closure $get) => $get("online")),

                Toggle::make("online")
                    ->label('Online'),
            ])->columnSpan(['lg' => 2]),
    ]);
}
```

## Documentation

For the full documentation, check [here](./docs/index.md).

## Testing

```bash
vendor/bin/pest
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Upgrading

Please see [UPGRADING](UPGRADING.md) for more information on how to upgrade to a new version.

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security Vulnerabilities

If you discover any security-related issues, please email info@codedor.be instead of using the issue tracker.

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
