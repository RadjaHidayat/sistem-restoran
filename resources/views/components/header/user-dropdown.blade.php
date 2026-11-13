@php
    $user = auth()->user();
    // Inisial nama untuk avatar, contoh "Siti Aminah" menjadi "SA"
    $inisial = collect(explode(' ', $user->nama))->filter()->map(fn ($k) => mb_substr($k, 0, 1))->take(2)->implode('');
@endphp

<x-ui.dropdown-menu>
    <x-ui.dropdown-menu.trigger class="dropdown-toggle flex items-center text-gray-700 dark:text-gray-400">
        <span class="me-3 flex h-11 w-11 items-center justify-center rounded-full bg-brand-500 text-sm font-semibold text-white uppercase">
            {{ $inisial }}
        </span>

        <span class="me-1 block max-w-32 truncate text-sm font-medium">{{ $user->nama }}</span>

        <x-svg.chevron-down class="size-5 text-gray-500 duration-200 dark:text-gray-400" x-bind:class="open ? 'rotate-180' : 'rotate-0'" />
    </x-ui.dropdown-menu.trigger>

    <x-ui.dropdown-menu.content align="end" :side-offset="12" class="w-65 p-3">
        <div class="px-3 py-2">
            <span class="block text-sm font-medium text-gray-700 dark:text-gray-300">{{ $user->nama }}</span>
            <span class="mt-0.5 block truncate text-xs text-gray-500 dark:text-gray-400">{{ $user->email }}</span>
            <span class="mt-2 inline-block rounded-full bg-brand-50 px-2.5 py-0.5 text-xs font-medium text-brand-600 capitalize dark:bg-brand-500/15 dark:text-brand-400">
                {{ $user->role->nama_role }}
            </span>
        </div>

        <x-ui.dropdown-menu.separator />

        <x-ui.dropdown-menu.item :to="route('profile')">
            <x-svg.user-circle class="size-6" />
            Profil Saya
        </x-ui.dropdown-menu.item>

        <x-ui.dropdown-menu.separator />

        <form id="logout-form" method="POST" action="{{ route('logout') }}" class="hidden">@csrf</form>
        <x-ui.dropdown-menu.item variant="destructive" x-on:click.capture="document.getElementById('logout-form').submit()">
            <x-svg.log-out />
            Keluar
        </x-ui.dropdown-menu.item>
    </x-ui.dropdown-menu.content>
</x-ui.dropdown-menu>