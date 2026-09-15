@extends('admin.layouts.guest')

@section('content')
    <h1 class="text-lg font-display font-bold text-charcoal-900 mb-6">Admin Sign In</h1>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('admin.login') }}" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required autofocus class="input-field">
        </div>

        <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1">Password</label>
            <input type="password" name="password" required class="input-field">
        </div>

        <button type="submit" class="btn-primary w-full">Sign In</button>
    </form>

    <p class="mt-6 text-xs text-center text-charcoal-400">
        Restricted area — JMCL JEANS LTD staff only.
    </p>
@endsection
