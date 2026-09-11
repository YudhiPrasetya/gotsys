<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="tallstackui_darkTheme()" >
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-gray-500 antialiased dark:bg-linear-to-b dark:from-neutral-950 dark:to-neutral-900">
    {{-- <body x-bind:class="{ 'dark bg-gray-700': darkTheme, 'bg-white': !darkTheme }"> --}}
        {{-- <x-button x-on:click="darkTheme = !darkTheme">Light / Dark Mode</x-button> --}}
        <div class="flex min-h-svh flex-col items-center justify-center gap-6 p-6 md:p-10">
            <div class="flex w-full max-w-1/3 flex-col gap-2">
                <div class="flex flex-col gap-6">
                    {{ $slot }}
                </div>
            </div>
        </div>
        {{-- {{ $slot }} --}}

        @livewireScripts
    </body>
</html>
