<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        <!-- Styles -->
        @livewireStyles
    </head>
    <body class="font-sans antialiased" style="
        --sidebar-color: {{ $activeColors->sidebar_color ?? '#151419' }};
        --header-color: {{ $activeColors->header_color ?? '#F56E0F' }};
        --search-area-color: {{ $activeColors->search_area_color ?? '#1B1B1E' }};
        --item-color: {{ $activeColors->item_color ?? '#262626' }};
        --button-area-color: {{ $activeColors->button_area_color ?? '#FBFBFB' }};
        --accent-color: {{ $activeColors->accent_color ?? '#F56E0F' }};
        --text-primary-color: {{ $activeColors->text_primary_color ?? '#FFFFFF' }};
        --text-secondary-color: {{ $activeColors->text_secondary_color ?? '#D1D5DB' }};
    ">
        <x-banner />

        <div class="h-screen flex flex-shrink-0">
            <!-- Sidebar - Primera columna - Dynamic -->
            <div class="w-20" style="background-color: var(--sidebar-color); width: 80px;">
                @livewire('navigation-menu')
            </div>

            <!-- Content - Segunda columna -->
            <div class="flex flex-1 flex-col" style="background-color: var(--sidebar-color);">
                @if (isset($header))
                    <header class="shadow flex-shrink-0" style="background-color: var(--header-color); height: 60px;">
                        <div class="px-4 py-3 sm:px-6 lg:px-8 h-full flex items-center">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                <main class="flex-1 overflow-auto">
                    {{ $slot }}
                </main>
            </div>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
