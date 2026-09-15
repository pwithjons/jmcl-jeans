<div class="flex gap-2 mb-8 border-b border-charcoal-100">
    <a href="{{ route('profile.edit') }}"
       class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('profile.edit') ? 'border-charcoal-900 text-charcoal-900' : 'border-transparent text-charcoal-400 hover:text-charcoal-600' }}">
        Profile
    </a>
    <a href="{{ route('account.orders.index') }}"
       class="px-4 py-2 text-sm font-medium border-b-2 {{ request()->routeIs('account.orders.*') ? 'border-charcoal-900 text-charcoal-900' : 'border-transparent text-charcoal-400 hover:text-charcoal-600' }}">
        My Orders
    </a>
</div>
