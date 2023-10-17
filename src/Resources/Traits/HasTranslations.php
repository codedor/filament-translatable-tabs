<?php

namespace Codedor\TranslatableTabs\Resources\Traits;

use Illuminate\Support\Arr;

trait HasTranslations
{
    protected function mutateFormDataBeforeCreate(array $data): array
    {
        return $this->mutateData($data);
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        return $this->mutateData($data);
    }

    protected function mutateFormDataBeforeFill(array $data): array
    {
        $record = $this->getRecord();
        foreach ($record->getTranslatableAttributes() as $field) {
            foreach ($record->getTranslatedLocales($field) as $locale) {
                $data[$locale][$field] = $record->getTranslation($field, $locale);
            }
        }

        return $data;
    }

    protected function mutateData(array $data): array
    {
        $model = app($this->getModel());
        foreach (Arr::except($data, $model->getFillable()) as $locale => $values) {
            if (! is_array($values)) {
                continue;
            }

            foreach (Arr::only($values, $model->getTranslatableAttributes()) as $key => $value) {
                $data[$key][$locale] = $value;
            }

            // unset($data[$locale]);
        }

        return $data;
    }
}
