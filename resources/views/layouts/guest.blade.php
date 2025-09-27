<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AllSports') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @if(app()->environment('local') || env('VITE_ENABLED', 'true') === 'true')
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @else
            <!-- Fallback CSS for production without Vite -->
            <script src="https://cdn.tailwindcss.com"></script>
            <style>
                /* Custom AllSports styles */
                .btn-primary {
                    @apply bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded;
                }
                .btn-secondary {
                    @apply bg-gray-600 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded;
                }
                .form-input {
                    @apply block w-full px-3 py-2 border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-blue-500 focus:border-blue-500;
                }
            </style>
            <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
        @endif
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gray-100">
            <div class="text-center mb-6">
                <a href="/" class="flex flex-col items-center">
                    <div class="text-4xl mb-2">⚽</div>
                    <h1 class="text-2xl font-bold text-gray-800">AllSports</h1>
                    <p class="text-sm text-gray-600">아마추어 스포츠 팀 플랫폼</p>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white shadow-md overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>
        </div>
    </body>
</html>
