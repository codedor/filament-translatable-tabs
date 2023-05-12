<?php

namespace Codedor\TranslatableTabs\Tables;

use Closure;
use Filament\Tables\Columns\Column;

class LocalesColumn extends Column
{
    protected string $view = 'tables.columns.locales-column';

    protected string $resourceAction = 'edit';

    protected array|Closure $locales;

    public function locales(array|Closure $locales)
    {
        $this->locales = $locales;

        return $this;
    }

    public function getLocales(): array
    {
        return $this->evaluate($this->locales);
    }

    public function resourceAction(string $resourceAction)
    {
        $this->resourceAction = $resourceAction;

        return $this;
    }

    public function getResourceUrl(string $locale): string
    {
        $livewire = $this->getLivewire();
        $resource = $livewire::getResource();

        return $resource::getUrl($this->resourceAction, ['record' => $this->getRecord(), 'locale' => "-{$locale}-tab"]);
    }
}
