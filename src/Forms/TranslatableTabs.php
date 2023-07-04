<?php

namespace Codedor\TranslatableTabs\Forms;

use Closure;
use Filament\Forms\Components\Component;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Support\Concerns\HasExtraAlpineAttributes;
use Livewire\Component as Livewire;

class TranslatableTabs extends Component
{
    use HasExtraAlpineAttributes;

    protected string $view = 'forms::components.tabs';

    public int|Closure $activeTab = 1;

    public array|Closure $defaultFields = [];

    public array|Closure $translatableFields = [];

    public array|Closure $locales = [];

    public null|string|Closure $icon = null;

    public null|string|Closure $iconColor = null;

    final public function __construct(string $label)
    {
        $this->label($label);
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

    public function isTabPersistedInQueryString(): bool
    {
        return true;
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

    public function translatableFields(array|Closure $translatableFields): static
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

    public function getIcon(string $locale): null|string
    {
        return $this->evaluate($this->icon, [
            'locale' => $locale,
        ]);
    }

    public function iconColor(null|string|Closure $iconColor): static
    {
        $this->iconColor = $iconColor;

        return $this;
    }

    public function getIconColor(string $locale): null|string
    {
        return $this->evaluate($this->iconColor, [
            'locale' => $locale,
        ]);
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
                ->icon(fn (Closure $get) => $this->getIcon($locale) ?? ($get("{$locale}.online") ? 'heroicon-o-status-online' : 'heroicon-o-status-offline'))
                ->iconColor(fn (Closure $get) => $this->getIconColor($locale) ?? ($get("{$locale}.online") ? 'success' : 'danger'))
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
