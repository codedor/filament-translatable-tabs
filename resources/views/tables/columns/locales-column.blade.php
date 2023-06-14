<div>
    @foreach ($getLocales() as $locale)
        @php
            $stateColor = match ($getRecord()->getTranslation($getName(), $locale)) {
                default => \Illuminate\Support\Arr::toCssClasses(['text-danger-700 bg-danger-500/10', 'dark:text-danger-500' => config('tables.dark_mode')]),
                true => \Illuminate\Support\Arr::toCssClasses(['text-success-700 bg-success-500/10', 'dark:text-success-500' => config('tables.dark_mode')]),
            };
        @endphp
        <a
            @class([
                'inline-flex items-center justify-center space-x-1 rtl:space-x-reverse min-h-6 px-2 py-0.5 text-sm font-medium tracking-tight rounded-xl whitespace-nowrap',
                $stateColor => $stateColor,
            ])
            href="{{ $getResourceUrl($locale) }}"
        >
            {{ $locale }}
        </a>
    @endforeach
</div>
