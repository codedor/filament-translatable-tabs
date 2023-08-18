<div>
    @foreach ($getLocales() as $locale)
        <a
            href="{{ $getResourceUrl($locale) }}"
            @style([
                match ($getRecord()->getTranslation($getName(), $locale)) {
                    true => \Filament\Support\get_color_css_variables('success', shades: [500, 700]),
                    default => \Filament\Support\get_color_css_variables('danger', shades: [500, 700]),
                }
            ])
            class="
                text-custom-700 bg-custom-500/10 dark:text-custom-500
                rtl:space-x-reverse min-h-6 px-2 py-0.5 text-sm font-medium tracking-tight
                inline-flex items-center justify-center space-x-1
                rounded-xl whitespace-nowrap
            "
        >
            {{ $locale }}
        </a>
    @endforeach
</div>
