# Translatable Tabs for Filament

## Introduction

Adds a tab per locale and adds integration for [spatie/laravel-translatable](https://spatie.be/docs/laravel-translatable/v6/introduction) in Filament.

![translatable-tabs.png](translatable-tabs.png)

## Installation

You can install the package via composer:

```bash
composer require codedor/filament-translatable-tabs
```

## Getting started

Add the TranslatableTabs to your resource form:

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

The default fields are the non-translatable fields and the translatable fields will be the fields that are also defined in the `$translatable` property on the model.

And add the `HasTranslations` trait to your pages with a form on (will be mostly Create and Edit):

```php
use Codedor\TranslatableTabs\Resources\Traits\HasTranslations;
```

This trait is necessary to save the translations together with your other fields. Since we have to manipulate the data after the form is submitted.
