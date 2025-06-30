<?php

use Codedor\TranslatableTabs\Tests\Fixtures\TestForm;

use function Pest\Livewire\livewire;

it('can render translatable tabs with default fields and translatable fields', function () {
    livewire(TestForm::class)
        ->assertFormFieldExists('default.working_title')
        ->assertFormFieldExists('en.title')
        ->assertFormFieldExists('en.online')
        ->assertFormFieldExists('nl.title')
        ->assertFormFieldExists('nl.online');
});
