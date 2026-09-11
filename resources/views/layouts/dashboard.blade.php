<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" x-data="tallstackui_darkTheme()">
    <head>
        @include('partials.head')
    </head>
    <body x-bind:class="{ 'dark bg-gray-700': darkTheme, 'bg-gray-400': !darkTheme }">
        <x-dialog />
        <x-toast />
        <x-layout>
            <x-slot:header>
                <x-layout.header>
                    <x-slot:left>
                        <span class="text-gray-700 dark:text-white">Dashboard</span>
                    </x-slot:left>

                    <x-slot:middle>
                        <x-input icon="magnifying-glass" placeholder="Search" sm />
                    </x-slot:middle>

                    <x-slot:right>
                        <x-dropdown text="Hello, {{ auth()->user()->name }}">
                            <x-slot:header>
                                <x-theme-switch simple />
                            </x-slot:header>
                            <x-dropdown.items text="Profile" />
                            {{-- <x-dropdown.items text="Logout" wire:click="logout" /> --}}
                            <x-dropdown.items text="Logout" href="/logout" wire:navigate.hover />
                        </x-dropdown>
                    </x-slot:right>
                </x-layout.header>
            </x-slot:header>

            <x-slot:menu>
                <x-side-bar collapsible thin-scroll>
                    <x-slot:brand>
                        <div class="flex justify-center py-4">
                            <img src="{{ asset('images/Yudhi.png') }}" class="h-10 w-10" />
                        </div>
                    </x-slot:brand>
                    <x-slot:brand-collapsed>
                        <div class="flex justify-center py-4">
                            <img src="{{ asset('images/Yudhi.png') }}" class="h-8 w-8" />
                        </div>
                    </x-slot:brand-collapsed>

                    <x-side-bar.item text="Home" icon="home" route="/" wire:navigate.hover current />
                    <x-side-bar.item text="Notifications" icon="bell" route="#">
                        <x-slot:badge>5</x-slot:badge>
                    </x-side-bar.item>

                    {{-- <x-side-bar.item text="Messages" icon="envelope" badge-color="blue" route="#">
                        <x-slot:badge>3</x-slot:badge>
                    </x-side-bar.item> --}}

                    <x-side-bar.separator text="Configurations" line />
                    <x-side-bar.item text="Settings" icon="cog-6-tooth" opened>
                        {{-- <x-side-bar.item text="Manage Permissions" route="/permission" wire:navigate.hover /> --}}
                        <x-side-bar.item text="Manage Roles" route="/roles" wire:navigate.hover />
                        <x-side-bar.item text="Manage Users" route="/users" wire:navigate.hover />
                    </x-side-bar.item>

                    <x-side-bar.separator text="Data Master" line />
                    <x-side-bar.item text="Data Master" icon="circle-stack" opened>
                        <x-side-bar.item text="Customer" route="/customer" wire:navigate.hover />
                        <x-side-bar.item text="Process Sequence" route="/process_sequence" wire:navigate.hover />
                        <x-side-bar.item text="Order" route="/order" wire:navigate.hover />
                        <x-side-bar.item text="Process Sequence Order" route="/process_sequence_order" wire:navigate.hover />
                    </x-side-bar.item>

                    <x-side-bar.separator text="Transaction" line />
                    <x-side-bar.item text="Transaction" icon="arrow-path-rounded-square" opened>
                        <x-side-bar.item text="Progress Order" route="/progress_order" wire:navigate.hover />
                    </x-side-bar.item>

                    <x-side-bar.separator text="Reports" line />
                    <x-side-bar.item text="Reports" icon="document-chart-bar" href="#" />

                    <x-slot:footer>
                        <p class="text-sm text-gray-500">gotsys-v1</p>
                    </x-slot:footer>
                </x-side-bar>
            </x-slot:menu>

            {{ $slot }}

        </x-layout>

        @livewireScripts
    </body>
</html>
