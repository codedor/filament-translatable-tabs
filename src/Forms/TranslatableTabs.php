<?php

namespace Codedor\TranslatableTabs\Forms;

use Closure;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Filament\Schemas\Contracts\HasRenderHookScopes;
use Filament\Schemas\Schema;
use Filament\Support\Concerns\CanBeContained;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Livewire\Component as Livewire;

class TranslatableTabs extends Tabs
{
    public bool|Closure $persistInQueryString = true;

    public array|Closure $defaultFields = [];

    public null|array|Closure $extraTabs = null;

    public Closure $translatableFields;

    public array|Closure $locales = [];

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan(['lg' => 2]);

        $this->afterStateHydrated(static function (TranslatableTabs $component, string|array|null $state, Livewire $livewire): void {
            if (blank($state)) {
                $component->state([]);

                return;
            }

            $record = method_exists($livewire, 'getRecord') ? $livewire->getRecord() : null;

            if (! $record || ! method_exists($record, 'getTranslatableAttributes')) {
                return;
            }

            foreach ($record->getTranslatableAttributes() as $field) {
                foreach ($record->getTranslatedLocales($field) as $locale) {
                    $value = $record->getTranslation($field, $locale);

                    if ($value instanceof Arrayable) {
                        $value = $value->toArray();
                    }

                    $state[$locale][$field] = $value;
                }
            }

            $component->state($state);
        });
    }

    public function dehydrateState(array &$state, bool $isDehydrated = true): void
    {
        parent::dehydrateState($state, $isDehydrated);

        $model = app($this->getModel());

        if (
            ! $model
            || ! method_exists($model, 'getFillable')
            || ! method_exists($model, 'getTranslatableAttributes')
        ) {
            return;
        }

        foreach (Arr::except($state['data'] ?? [], $model->getFillable()) as $locale => $values) {
            if (! is_array($values)) {
                continue;
            }

            foreach (Arr::only($values, $model->getTranslatableAttributes()) as $key => $value) {
                $state['data'][$key][$locale] = $value;
            }
        }
    }

    public function getTabQueryStringKey(): ?string
    {
        return 'locale';
    }

    public function defaultFields(array|Closure $defaultFields): static
    {
        $this->defaultFields = $defaultFields;

        return $this;
    }

    public function extraTabs(null|array|Closure $extraTabs): static
    {
        $this->extraTabs = $extraTabs;

        return $this;
    }

    public function translatableFields(Closure $translatableFields): static
    {
        $tabs = [
            Tabs\Tab::make('Default')->schema($this->evaluate($this->defaultFields)),
        ];

        if (! is_null($this->extraTabs)) {
            $tabs = array_merge($tabs, $this->evaluate($this->extraTabs));
        }

        foreach ($this->getLocales() as $locale) {
            $tabs[] = Tabs\Tab::make($locale)
                ->schema($this->evaluate($translatableFields, [
                    'locale' => $locale,
                ]))
                ->statePath($locale)
                ->iconPosition('after')
                // ->iconColor((fn (Get $get) => ($get("{$locale}.online") ? 'success' : 'danger')))
                ->icon(fn (Get $get) => $get("{$locale}.online") ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle')
                ->badge(function (Livewire $livewire) use ($locale) {
                    if ($livewire->getErrorBag()->has("data.{$locale}.*")) {
                        $count = count($livewire->getErrorBag()->get("data.{$locale}.*"));

                        return trans_choice('{1} :count error|[2,*] :count errors', $count, [
                            'count' => $count,
                        ]);
                    }

                    return null;
                });
        }

        $this->tabs($tabs);

        return $this;
    }

    public function locales(array|Closure $locales): static
    {
        $this->locales = $locales;

        return $this;
    }

    public function getLocales(): array
    {
        return $this->evaluate($this->locales);
    }
}
