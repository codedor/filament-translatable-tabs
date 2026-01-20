<?php

use Livewire\Livewire;
use Wotz\TranslatableTabs\Tests\Fixtures\TestForm;

it('can render translatable tabs with default fields and translatable fields', function () {
    Livewire::test(TestForm::class)
        ->assertFormFieldExists('default.working_title')
        ->assertFormFieldExists('en.title')
        ->assertFormFieldExists('en.online')
        ->assertFormFieldExists('nl.title')
        ->assertFormFieldExists('nl.online');
});
