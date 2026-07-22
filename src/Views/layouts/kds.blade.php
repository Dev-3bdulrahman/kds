<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" dir="{{ app()->getLocale() === 'ar' ? 'rtl' : 'ltr' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Laravel') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
    @livewireStyles
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { overflow: hidden; }
    </style>
</head>
<body class="bg-gray-900 antialiased">
    {{ $slot }}

    @livewireScripts
    @stack('scripts')
</body>
</html>
