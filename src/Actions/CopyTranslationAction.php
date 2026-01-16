<?php

namespace Codedor\TranslatableTabs\Actions;

use Filament\Actions\Action;
use Filament\Forms\Components\Select;
use Illuminate\Support\Str;
use Livewire\Component;
use Throwable;

class CopyTranslationAction extends Action
{
    private array $locales = [];

    public static function getDefaultName(): ?string
    {
        return 'copy-translation';
    }

    public function locales(array $locales): self
    {
        $this->locales = $locales;

        return $this;
    }

    public function getLocales(): array
    {
        return $this->locales;
    }

    protected function setUp(): void
    {
        parent::setUp();

        $this->label(__('filament-translatable-tabs::copy-translation.label'));

        $this->modalHeading(fn (Component $livewire): string => __('filament-translatable-tabs::copy-translation.copy translation for :label', [
            'label' => $this->getRecord() ? $this->getRecordTitle() : Str::headline($livewire::getResource()::getModelLabel()),
        ]));

        $this->successNotificationTitle(__('filament-translatable-tabs::copy-translation.success notification'));

        $this->schema(fn (): array => [
            Select::make('from_locale')
                ->options($this->getLocales())
                ->required(),

            Select::make('to_locale')
                ->options($this->getLocales())
                ->required()
                ->different('from_locale'),
        ]);

        $this->action(function (array $data, Component $livewire) {
            try {
                $livewire->data[$data['to_locale']] = $livewire->data[$data['from_locale']];

                $this->success();
            } catch (Throwable $e) {
                $this->failure();
            }
        });
    }
}
