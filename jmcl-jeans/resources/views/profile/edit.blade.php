@extends('layouts.app')

@section('content')
<div class="container mx-auto max-w-2xl py-12 space-y-10">

    @include('partials.account-nav')

    <div>
        <h1 class="section-heading">My Account</h1>
        <p class="text-charcoal-500 mt-1">Manage your profile information and password.</p>
    </div>

    {{-- Profile info --}}
    <div class="bg-white border border-charcoal-100 rounded-lg p-6">
        <h2 class="font-semibold text-lg mb-4">Profile Information</h2>

        @if ($errors->any())
            <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('patch')

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Name</label>
                <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="input-field">
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Email</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="input-field">
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}" class="input-field">
            </div>

            <button type="submit" class="btn-primary">Save Changes</button>
        </form>
    </div>

    {{-- Password --}}
    <div class="bg-white border border-charcoal-100 rounded-lg p-6">
        <h2 class="font-semibold text-lg mb-4">Update Password</h2>

        <form method="POST" action="{{ route('profile.password.update') }}" class="space-y-4">
            @csrf
            @method('put')

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Current Password</label>
                <input type="password" name="current_password" required class="input-field">
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">New Password</label>
                <input type="password" name="password" required class="input-field">
            </div>

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Confirm New Password</label>
                <input type="password" name="password_confirmation" required class="input-field">
            </div>

            <button type="submit" class="btn-primary">Update Password</button>
        </form>
    </div>

    {{-- Danger zone --}}
    <div class="bg-white border border-red-200 rounded-lg p-6">
        <h2 class="font-semibold text-lg mb-2 text-red-700">Delete Account</h2>
        <p class="text-sm text-charcoal-500 mb-4">This will permanently delete your account. This action cannot be undone.</p>

        <form method="POST" action="{{ route('profile.destroy') }}" class="space-y-4"
              onsubmit="return confirm('Are you sure you want to delete your account?');">
            @csrf
            @method('delete')

            <div>
                <label class="block text-sm font-medium text-charcoal-700 mb-1">Confirm Password</label>
                <input type="password" name="password" required class="input-field max-w-xs">
            </div>

            <button type="submit" class="inline-flex items-center px-6 py-3 bg-red-600 text-white text-sm font-semibold uppercase rounded-md hover:bg-red-700">
                Delete Account
            </button>
        </form>
    </div>

</div>
@endsection
