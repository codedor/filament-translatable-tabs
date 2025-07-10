<?php

namespace Codedor\TranslatableTabs\Forms;

use Closure;
use Codedor\TranslatableTabs\Forms\Casts\TranslatableTabsStateCast;
use Filament\Schemas\Components\Tabs;
use Filament\Schemas\Components\Utilities\Get;
use Illuminate\Support\Arr;
use Livewire\Component as Livewire;

class TranslatableTabs extends Tabs
{
    public array|Closure $defaultFields = [];

    public null|array|Closure $extraTabs = null;

    public Closure $translatableFields;

    public array|Closure $locales = [];

    public null|string|Closure $icon = null;

    protected function setUp(): void
    {
        parent::setUp();

        $this->columnSpan(['lg' => 2]);

        $this->persistTabInQueryString('locale');

        $this->stateCast(fn (Livewire $livewire) => app(TranslatableTabsStateCast::class, ['livewire' => $livewire]));

        $this->tabs([]);
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
        $this->translatableFields = $translatableFields;

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

    public function icon(null|string|Closure $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(string $locale): ?string
    {
        return $this->evaluate($this->icon, [
            'locale' => $locale,
        ]);
    }

    public function getDefaultChildComponents(): array
    {
        $tabs = [
            \Filament\Schemas\Components\Tabs\Tab::make('Default')
                ->schema($this->evaluate($this->defaultFields))
                ->id('default')
                ->key('default'),
        ];

        if (! is_null($this->extraTabs)) {
            $tabs = array_merge($tabs, $this->evaluate($this->extraTabs));
        }

        foreach ($this->getLocales() as $locale) {
            $tabs[] = \Filament\Schemas\Components\Tabs\Tab::make($locale)
                ->schema($this->evaluate($this->translatableFields, [
                    'locale' => $locale,
                ]))
                ->key($locale)
                ->id($locale)
                ->statePath($locale)
                ->iconPosition('after')
                ->icon(fn (Get $get) => $this->getIcon($locale) ?? ($get("{$locale}.online") ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'))
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

        return $tabs;
    }
}
