@props(['locales' => \App\Enums\Locale::cases()])

<flux:dropdown position="bottom" align="end">
    <flux:button variant="ghost" size="sm" icon="language">
        {{ strtoupper(app()->getLocale()) }}
    </flux:button>

    <flux:menu>
        @foreach ($locales as $locale)
            <flux:menu.item
                :href="route('locale.set', $locale->value)"
                :icon-trailing="app()->getLocale() === $locale->value ? 'check' : null"
            >
                {{ $locale->label() }}
            </flux:menu.item>
        @endforeach
    </flux:menu>
</flux:dropdown>
