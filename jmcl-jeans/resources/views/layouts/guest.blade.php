<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'JMCL JEANS LTD' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-charcoal-50 min-h-screen flex flex-col items-center justify-center px-4">

    <a href="{{ route('home') }}" class="font-display text-3xl font-bold tracking-tight text-charcoal-900 mb-8">
        JMCL <span class="text-denim-600">JEANS</span>
    </a>

    <div class="w-full max-w-md bg-white rounded-lg shadow-sm border border-charcoal-100 p-8">
        @yield('content')
    </div>

</body>
</html>
