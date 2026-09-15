@extends('layouts.guest')

@section('content')
    <h1 class="text-xl font-display font-bold text-charcoal-900 mb-6">Create your account</h1>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <div>
            <label for="name" class="block text-sm font-medium text-charcoal-700 mb-1">Full Name</label>
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus class="input-field">
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-charcoal-700 mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required class="input-field">
        </div>

        <div>
            <label for="phone" class="block text-sm font-medium text-charcoal-700 mb-1">Phone (optional)</label>
            <input id="phone" type="text" name="phone" value="{{ old('phone') }}" class="input-field">
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-charcoal-700 mb-1">Password</label>
            <input id="password" type="password" name="password" required class="input-field">
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-charcoal-700 mb-1">Confirm Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required class="input-field">
        </div>

        <button type="submit" class="btn-primary w-full">Create Account</button>
    </form>

    <p class="mt-6 text-sm text-center text-charcoal-500">
        Already have an account?
        <a href="{{ route('login') }}" class="text-denim-600 font-medium hover:underline">Sign in</a>
    </p>
@endsection
