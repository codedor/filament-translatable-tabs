<?php

namespace Codedor\TranslatableTabs\Tests\Fixtures;

use Codedor\TranslatableTabs\Forms\TranslatableTabs;
use Filament\Forms\Components\Checkbox;
use Filament\Forms\Components\TextInput;
use Filament\Schemas\Concerns\InteractsWithSchemas;
use Filament\Schemas\Contracts\HasSchemas;
use Filament\Schemas\Schema;
use Livewire\Component;

class TestForm extends Component implements HasSchemas
{
    use InteractsWithSchemas;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();
    }

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TranslatableTabs::make()
                    ->locales(['en', 'nl'])
                    ->defaultFields([
                        TextInput::make('working_title'),
                    ])
                    ->translatableFields(fn (string $locale) => [
                        TextInput::make('title'),
                        Checkbox::make('online'),
                    ]),
            ])
            ->statePath('data');
    }

    public function render(): string
    {
        return <<<'HTML'
        <div>
            <form wire:submit="create">
                {{ $this->form }}

                <button type="submit">
                    Submit
                </button>
            </form>

            <x-filament-actions::modals />
        </div>
        HTML;
    }
}
