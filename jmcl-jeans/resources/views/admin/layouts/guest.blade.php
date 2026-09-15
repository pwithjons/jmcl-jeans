<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin | JMCL JEANS LTD' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-charcoal-950 min-h-screen flex flex-col items-center justify-center px-4">

    <div class="font-display text-2xl font-bold tracking-tight text-white mb-8">
        JMCL <span class="text-denim-400">JEANS</span> <span class="text-charcoal-400 text-base font-sans font-normal">/ Admin</span>
    </div>

    <div class="w-full max-w-sm bg-white rounded-lg shadow-lg p-8">
        @yield('content')
    </div>

</body>
</html>
