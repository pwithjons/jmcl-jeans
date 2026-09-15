<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'JMCL JEANS LTD — Premium Denim' }}</title>
    <meta name="description" content="{{ $metaDescription ?? 'Premium denim and modern fits from JMCL JEANS LTD.' }}">
    <link rel="canonical" href="{{ url()->current() }}">

    {{-- Open Graph / social share preview --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="JMCL JEANS LTD">
    <meta property="og:title" content="{{ $title ?? 'JMCL JEANS LTD — Premium Denim' }}">
    <meta property="og:description" content="{{ $metaDescription ?? 'Premium denim and modern fits from JMCL JEANS LTD.' }}">
    <meta property="og:url" content="{{ url()->current() }}">
    @if (isset($ogImage))
        <meta property="og:image" content="{{ $ogImage }}">
    @endif

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white min-h-screen flex flex-col text-charcoal-900">

    <div class="bg-charcoal-900 text-white text-xs text-center py-2 px-4">
        Cash on Delivery available nationwide — free delivery on qualifying orders.
    </div>

    <header class="border-b border-charcoal-100 sticky top-0 bg-white/95 backdrop-blur z-40">
        <div class="container mx-auto px-4 py-4 flex items-center justify-between gap-6">
            <a href="{{ route('home') }}" class="font-display text-2xl font-bold tracking-tight shrink-0">
                JMCL <span class="text-denim-600">JEANS</span>
            </a>

            <nav class="hidden md:flex items-center gap-8 text-sm font-medium">
                <a href="{{ route('shop.index') }}" class="hover:text-denim-600">Shop All</a>
                @foreach ($navCategories ?? [] as $navCategory)
                    <a href="{{ route('categories.show', $navCategory) }}" class="hover:text-denim-600">{{ $navCategory->name }}</a>
                @endforeach
            </nav>

            <form action="{{ route('search.index') }}" method="GET" class="hidden md:block flex-1 max-w-xs">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..."
                       class="w-full text-sm border border-charcoal-200 rounded-full px-4 py-2 focus:border-denim-500 focus:ring-denim-500">
            </form>

            <div class="flex items-center gap-5 text-sm font-medium shrink-0">
                @auth('web')
                    <a href="{{ route('profile.edit') }}" class="hover:text-denim-600 hidden sm:inline">My Account</a>
                @else
                    <a href="{{ route('login') }}" class="hover:text-denim-600 hidden sm:inline">Login</a>
                @endauth

                <a href="{{ route('cart.index') }}" class="relative hover:text-denim-600" aria-label="Cart">
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 1.94-4.788 2.442-7.401.108-.563-.35-1.099-.924-1.099H5.106M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z" />
                    </svg>
                    @if (($cartCount ?? 0) > 0)
                        <span class="absolute -top-2 -right-2 bg-denim-600 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center">{{ $cartCount }}</span>
                    @endif
                </a>

                @auth('web')
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="hover:text-denim-600 hidden sm:inline">Logout</button>
                    </form>
                @else
                    <a href="{{ route('register') }}" class="btn-secondary !px-4 !py-2 hidden sm:inline-flex">Register</a>
                @endauth
            </div>
        </div>

        <form action="{{ route('search.index') }}" method="GET" class="md:hidden px-4 pb-3">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Search products..."
                   class="w-full text-sm border border-charcoal-200 rounded-full px-4 py-2">
        </form>
    </header>

    <main class="flex-1">
        @if (session('status'))
            <div class="container mx-auto mt-4 px-4">
                <div class="rounded-md bg-green-50 border border-green-200 text-green-800 text-sm px-4 py-3">
                    {{ session('status') }}
                </div>
            </div>
        @endif
        @if (session('error'))
            <div class="container mx-auto mt-4 px-4">
                <div class="rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
                    {{ session('error') }}
                </div>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-charcoal-950 text-charcoal-300 mt-20">
        <div class="container mx-auto px-4 py-14 grid grid-cols-2 md:grid-cols-4 gap-8">
            <div class="col-span-2 md:col-span-1">
                <div class="font-display text-xl font-bold text-white mb-3">JMCL <span class="text-denim-400">JEANS</span></div>
                <p class="text-sm text-charcoal-400">Premium denim, modern fits, built for everyday wear.</p>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-3">Shop</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('shop.index') }}" class="hover:text-white">All Products</a></li>
                    @foreach ($navCategories ?? [] as $navCategory)
                        <li><a href="{{ route('categories.show', $navCategory) }}" class="hover:text-white">{{ $navCategory->name }}</a></li>
                    @endforeach
                </ul>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-3">Company</h4>
                <ul class="space-y-2 text-sm">
                    <li><a href="{{ route('about') }}" class="hover:text-white">About Us</a></li>
                    <li><a href="{{ route('contact') }}" class="hover:text-white">Contact Us</a></li>
                    <li><a href="{{ route('privacy') }}" class="hover:text-white">Privacy Policy</a></li>
                    <li><a href="{{ route('terms') }}" class="hover:text-white">Terms &amp; Conditions</a></li>
                </ul>
            </div>
            <div>
                <h4 class="text-white text-sm font-semibold mb-3">Get in Touch</h4>
                <p class="text-sm text-charcoal-400">{{ \App\Models\Setting::get('store_email', 'info@jmcljeans.test') }}</p>
                <p class="text-sm text-charcoal-400">{{ \App\Models\Setting::get('store_phone', '') }}</p>
            </div>
        </div>
        <div class="border-t border-charcoal-800 py-5 text-center text-xs text-charcoal-500">
            &copy; {{ date('Y') }} JMCL JEANS LTD. All rights reserved.
        </div>
    </footer>

    @stack('scripts')

</body>
</html>
