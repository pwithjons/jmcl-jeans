<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin | JMCL JEANS LTD' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-charcoal-50 min-h-screen flex">

    <aside class="w-64 bg-charcoal-950 text-charcoal-200 flex-shrink-0 hidden md:flex md:flex-col">
        <div class="px-6 py-5 font-display text-lg font-bold text-white border-b border-charcoal-800">
            JMCL <span class="text-denim-400">JEANS</span>
        </div>
        <nav class="flex-1 px-3 py-4 space-y-1 text-sm">
            <a href="{{ route('admin.dashboard') }}"
               class="block px-3 py-2 rounded-md {{ request()->routeIs('admin.dashboard') ? 'bg-charcoal-800 text-white' : 'hover:bg-charcoal-900' }}">
                Dashboard
            </a>
            <a href="{{ route('admin.products.index') }}"
               class="block px-3 py-2 rounded-md {{ request()->routeIs('admin.products.*') ? 'bg-charcoal-800 text-white' : 'hover:bg-charcoal-900' }}">
                Products
            </a>
            <a href="{{ route('admin.categories.index') }}"
               class="block px-3 py-2 rounded-md {{ request()->routeIs('admin.categories.*') ? 'bg-charcoal-800 text-white' : 'hover:bg-charcoal-900' }}">
                Categories
            </a>
            <a href="{{ route('admin.subcategories.index') }}"
               class="block px-3 py-2 rounded-md {{ request()->routeIs('admin.subcategories.*') ? 'bg-charcoal-800 text-white' : 'hover:bg-charcoal-900' }}">
                Subcategories
            </a>
            <a href="{{ route('admin.orders.index') }}"
               class="block px-3 py-2 rounded-md {{ request()->routeIs('admin.orders.*') ? 'bg-charcoal-800 text-white' : 'hover:bg-charcoal-900' }}">
                Orders
            </a>
            <a href="{{ route('admin.customers.index') }}"
               class="block px-3 py-2 rounded-md {{ request()->routeIs('admin.customers.*') ? 'bg-charcoal-800 text-white' : 'hover:bg-charcoal-900' }}">
                Customers
            </a>
            <a href="{{ route('admin.coupons.index') }}"
               class="block px-3 py-2 rounded-md {{ request()->routeIs('admin.coupons.*') ? 'bg-charcoal-800 text-white' : 'hover:bg-charcoal-900' }}">
                Coupons
            </a>
            <a href="{{ route('admin.shipping.edit') }}"
               class="block px-3 py-2 rounded-md {{ request()->routeIs('admin.shipping.*') ? 'bg-charcoal-800 text-white' : 'hover:bg-charcoal-900' }}">
                Shipping
            </a>
            <a href="{{ route('admin.reports.index') }}"
               class="block px-3 py-2 rounded-md {{ request()->routeIs('admin.reports.*') ? 'bg-charcoal-800 text-white' : 'hover:bg-charcoal-900' }}">
                Reports
            </a>
            {{-- Site-wide Settings page arrives alongside the storefront
                 in Phase 4 (it configures things the storefront reads). --}}
            @can('manage-admins')
                <a href="{{ route('admin.admins.index') }}"
                   class="block px-3 py-2 rounded-md {{ request()->routeIs('admin.admins.*') ? 'bg-charcoal-800 text-white' : 'hover:bg-charcoal-900' }}">
                    Admin Accounts
                </a>
            @endcan
        </nav>
        <div class="px-3 py-4 border-t border-charcoal-800">
            <div class="px-3 text-xs text-charcoal-400 mb-2">
                {{ auth('admin')->user()->name }}
                <span class="block text-charcoal-500">{{ auth('admin')->user()->role === 'super_admin' ? 'Super Admin' : 'Admin' }}</span>
            </div>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-3 py-2 rounded-md hover:bg-charcoal-900 text-sm">
                    Logout
                </button>
            </form>
        </div>
    </aside>

    <div class="flex-1 flex flex-col min-w-0">
        <header class="bg-white border-b border-charcoal-200 px-6 py-4 md:hidden flex items-center justify-between">
            <span class="font-display font-bold">JMCL Admin</span>
            <form method="POST" action="{{ route('admin.logout') }}">
                @csrf
                <button type="submit" class="text-sm text-charcoal-500">Logout</button>
            </form>
        </header>

        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>

    @stack('scripts')

</body>
</html>
