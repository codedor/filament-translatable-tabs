<?php

namespace Codedor\TranslatableTabs\Resources\Traits;

use Filament\Actions\Action;
use Illuminate\Support\Js;

trait HasTranslations
{
    protected function getCancelFormAction(): Action
    {
        return Action::make('cancel')
            ->label(__('filament-panels::resources/pages/edit-record.form.actions.cancel.label'))
            ->alpineClickHandler('(window.location.href = ' . Js::from($this->previousUrl ?? static::getResource()::getUrl()) . ')')
            ->color('gray');
    }
}
