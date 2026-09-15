@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16 max-w-lg">
    <h1 class="section-heading mb-6">Contact Us</h1>

    @if ($errors->any())
        <div class="mb-4 rounded-md bg-red-50 border border-red-200 text-red-700 text-sm px-4 py-3">
            <ul class="list-disc list-inside space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('contact.submit') }}" class="space-y-4">
        @csrf
        <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1">Name</label>
            <input type="text" name="name" value="{{ old('name') }}" required class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1">Email</label>
            <input type="email" name="email" value="{{ old('email') }}" required class="input-field">
        </div>
        <div>
            <label class="block text-sm font-medium text-charcoal-700 mb-1">Message</label>
            <textarea name="message" rows="5" required class="input-field">{{ old('message') }}</textarea>
        </div>
        <button type="submit" class="btn-primary">Send Message</button>
    </form>

    <div class="mt-10 text-sm text-charcoal-500 space-y-1">
        <p>{{ \App\Models\Setting::get('store_email', 'info@jmcljeans.test') }}</p>
        <p>{{ \App\Models\Setting::get('store_phone', '') }}</p>
        <p>{{ \App\Models\Setting::get('store_address', '') }}</p>
    </div>
</div>
@endsection
