@extends('layouts.guest')

@section('content')
    <h1 class="text-xl font-display font-bold text-charcoal-900 mb-6">Sign in to your account</h1>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-4">
        @csrf

        <div>
            <label for="email" class="block text-sm font-medium text-charcoal-700 mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
                   class="input-field">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-charcoal-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required class="input-field">
        </div>

        <div class="flex items-center justify-between text-sm">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="remember" class="rounded border-charcoal-300">
                Remember me
            </label>
        </div>

        <button type="submit" class="btn-primary w-full">Sign In</button>
    </form>

    <p class="mt-6 text-sm text-center text-charcoal-500">
        Don't have an account?
        <a href="{{ route('register') }}" class="text-denim-600 font-medium hover:underline">Create one</a>
    </p>
@endsection
