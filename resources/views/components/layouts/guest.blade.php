<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>{{ $title ?? 'Web Psikolog' }} — Clinical Practice & Records</title>
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        @livewireStyles
    </head>
    <body class="sanctuary-bg font-sans text-slate-100 antialiased min-h-screen selection:bg-teal-500 selection:text-slate-950">
        {{ $slot }}
        @livewireScripts
    </body>
</html>
