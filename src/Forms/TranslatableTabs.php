<?php

namespace Codedor\TranslatableTabs\Forms;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Get;
use Filament\Support\Concerns\CanBeContained;
use Filament\Support\Concerns\CanPersistTab;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Livewire\Component as Livewire;

class TranslatableTabs extends Component
{
    use CanBeContained;
    use HasExtraAlpineAttributes;
    use CanPersistTab;

    protected string $view = 'filament-forms::components.tabs';

    public int|Closure $activeTab = 1;

    public bool|Closure $persistInQueryString = true;

    public array|Closure $defaultFields = [];

    public Closure $translatableFields;

    public array|Closure $locales = [];

    public null|string|Closure $icon = null;

    final public function __construct(string $label)
    {
        $this->label($label);

        $this->columnSpan(['lg' => 2]);
    }

    public static function make(): static
    {
        $static = app(static::class, ['label' => 'Translations']);
        $static->configure();

        return $static;
    }

    public function getTabQueryStringKey(): ?string
    {
        return 'locale';
    }

    public function getActiveTab(): int
    {
        if ($this->isTabPersistedInQueryString()) {
            $queryStringTab = request()->query($this->getTabQueryStringKey());

            foreach ($this->getChildComponentContainer()->getComponents() as $index => $tab) {
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

    public function getChildComponents(): array
    {
        $tabs = [
            Tab::make('Default')
                ->schema($this->evaluate($this->defaultFields)),
        ];

        foreach ($this->evaluate($this->locales) as $locale) {
            $tabs[] = Tab::make($locale)
                ->schema($this->evaluate($this->translatableFields, [
                    'locale' => $locale,
                ]))
                ->statePath($locale)
                ->iconPosition('after')
                // ->iconColor((fn (Get $get) => ($get("{$locale}.online") ? 'success' : 'danger')))
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
