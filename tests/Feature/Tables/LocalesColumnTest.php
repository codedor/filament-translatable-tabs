<?php

use Codedor\TranslatableTabs\Tables\LocalesColumn;

it('can test', function () {
    $column = LocalesColumn::make('online')
        ->locales(['nl', 'fr']);

    expect($column)
        ->getLocales()->toBe(['nl', 'fr']);
});
