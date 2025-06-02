<?php

namespace Codedor\TranslatableTabs\Forms;

use Closure;
use Filament\Forms\Get;
use Filament\Schemas\Components\Concerns\HasLabel;
use Filament\Schemas\Contracts\HasRenderHookScopes;
use Filament\Support\Concerns\CanBeContained;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Support\Arr;
use Livewire\Component as Livewire;

class TranslatableTabs extends \Filament\Schemas\Components\Component
{
    use CanBeContained;
    use \Filament\Schemas\Components\Concerns\CanPersistTab;
    use HasExtraAlpineAttributes;
    use HasLabel;

    protected string $view = 'filament-schemas::components.tabs';

    public int|Closure $activeTab = 1;

    public bool|Closure $persistInQueryString = true;

    public array|Closure $defaultFields = [];

    public null|array|Closure $extraTabs = null;

    public Closure $translatableFields;

    public array|Closure $locales = [];

    public null|string|Closure $icon = null;

    protected bool | Closure $isVertical = false;

    protected string | Closure | null $livewireProperty = null;

    protected array $startRenderHooks = [];

    /**
     * @var array<string>
     */
    protected array $endRenderHooks = [];

    final public function __construct(string $label)
    {
        $this->label($label);

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

    public static function make(): static
    {
        $static = app(static::class, ['label' => 'Translations']);
        $static->configure();

        return $static;
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

    public function getActiveTab(): int
    {
        if ($this->isTabPersistedInQueryString()) {
            $queryStringTab = request()->query($this->getTabQueryStringKey());

            foreach ($this->getChildSchema()->getComponents() as $index => $tab) {
                if ($tab->getId() !== $queryStringTab) {
                    continue;
                }

                return $index + 1;
            }
        }

        return $this->evaluate($this->activeTab);
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

    public function persistInQueryString(bool|Closure $condition = true): static
    {
        $this->persistInQueryString = $condition;

        return $this;
    }

    public function isTabPersistedInQueryString(): bool
    {
        return $this->evaluate($this->persistInQueryString);
    }

    public function getDefaultChildComponents(): array
    {
        $tabs = [
            \Filament\Schemas\Components\Tabs\Tab::make('Default')->schema($this->evaluate($this->defaultFields)),
        ];

        if (! is_null($this->extraTabs)) {
            $tabs = array_merge($tabs, $this->evaluate($this->extraTabs));
        }

        foreach ($this->getLocales() as $locale) {
            $tabs[] = \Filament\Schemas\Components\Tabs\Tab::make($locale)
                ->schema($this->evaluate($this->translatableFields, [
                    'locale' => $locale,
                ]))
                ->statePath($locale)
                ->iconPosition('after')
                // ->iconColor((fn (Get $get) => ($get("{$locale}.online") ? 'success' : 'danger')))
                ->icon(fn (\Filament\Schemas\Components\Utilities\Get $get) => $this->getIcon($locale) ?? ($get("{$locale}.online") ? 'heroicon-o-check-circle' : 'heroicon-o-x-circle'))
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

    public function livewireProperty(string | Closure | null $property): static
    {
        $this->livewireProperty = $property;

        return $this;
    }

    public function getLivewireProperty(): ?string
    {
        return $this->evaluate($this->livewireProperty);
    }

    public function vertical(bool | Closure $condition = true): static
    {
        $this->isVertical = $condition;

        return $this;
    }

    public function isVertical(): bool
    {
        return (bool) $this->evaluate($this->isVertical);
    }

    /**
     * @param  array<string>  $hooks
     */
    public function startRenderHooks(array $hooks): static
    {
        $this->startRenderHooks = $hooks;

        return $this;
    }

    /**
     * @param  array<string>  $hooks
     */
    public function endRenderHooks(array $hooks): static
    {
        $this->endRenderHooks = $hooks;

        return $this;
    }

    /**
     * @return array<string>
     */
    public function getStartRenderHooks(): array
    {
        return $this->startRenderHooks;
    }

    /**
     * @return array<string>
     */
    public function getEndRenderHooks(): array
    {
        return $this->endRenderHooks;
    }

    /**
     * @return array<string>
     */
    public function getRenderHookScopes(): array
    {
        $livewire = $this->getLivewire();

        if (! ($livewire instanceof HasRenderHookScopes)) {
            return [];
        }

        return $livewire->getRenderHookScopes();
    }
}
