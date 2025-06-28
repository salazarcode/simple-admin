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
    <body class="font-sans antialiased">
        <x-banner />

        <div class="h-screen flex">
            <!-- Sidebar - Primera columna -->
            <div class="w-24" style="background-color: #133215;">
                @livewire('navigation-menu')
            </div>

            <!-- Content - Segunda columna -->
            <main class="flex flex-1 flex-col" style="background-color: #F3E8D3;">
                @if (isset($header))
                    <header class="shadow flex-shrink-0" style="background-color: #92B775; height: 80px;">
                        <div class="px-4 py-6 sm:px-6 lg:px-8 h-full flex items-center">
                            {{ $header }}
                        </div>
                    </header>
                @endif

                {{ $slot }}
            </main>
        </div>

        @stack('modals')

        @livewireScripts
    </body>
</html>
