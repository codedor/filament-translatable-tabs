<?php

namespace Codedor\TranslatableTabs\Forms\Casts;

use Codedor\TranslatableTabs\Forms\TranslatableTabs;
use Filament\Forms\Components\RichEditor;
use Filament\Schemas\Components\StateCasts\Contracts\StateCast;
use Illuminate\Contracts\Support\Arrayable;
use Livewire\Component;

class TranslatableTabsStateCast implements StateCast
{
    public function __construct(
        protected Component $livewire,
    ) {}

    public function get(mixed $state): string | array
    {
        // see how we can set the state here instead of using dehydrateState in TranslatableTabs
        return $state;
    }

    public function set(mixed $state): array
    {
        if (blank($state)) {
            return [];
        }

        $record = method_exists($this->livewire, 'getRecord') ? $this->livewire->getRecord() : null;

        if (! $record || ! method_exists($record, 'getTranslatableAttributes')) {
            return [];
        }

        foreach ($record->getTranslatableAttributes() as $field) {
            foreach ($record->getTranslatedLocales($field) as $locale) {
                $value = $record->getTranslation($field, $locale);

                if ($value instanceof Arrayable) {
                    $value = $value->toArray();
                }

                // RichEditor hack
                if (isset($state[$locale][$field]) && is_array($state[$locale][$field])) {
                    if (isset($state[$locale][$field]['type']) && $state[$locale][$field]['type'] === 'doc' && isset($state[$locale][$field]['content'])) {
                        $components = $this->livewire->form->getFlatComponents(withActions: false, withHidden: true);

                        if (isset($components["{$locale}.{$field}"]) && $components["{$locale}.{$field}"] instanceof RichEditor) {
                            $value = $components["{$locale}.{$field}"]->getTipTapEditor()
                                ->setContent($value)
                                ->getDocument();
                        }
                    }
                }

                $state[$locale][$field] = $value;
            }
        }

        return $state;
    }
}
