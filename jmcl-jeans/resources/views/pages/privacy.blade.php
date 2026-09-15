@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-16 max-w-2xl">
    <h1 class="section-heading mb-6">Privacy Policy</h1>
    <div class="prose text-charcoal-600 space-y-4 text-sm">
        <p>This Privacy Policy explains how JMCL JEANS LTD collects, uses, and protects information when you use this website.</p>
        <h2 class="font-semibold text-charcoal-900 pt-2">Information We Collect</h2>
        <p>When you register an account or place an order, we collect your name, email address, phone number, and delivery address. This information is used solely to process and deliver your orders.</p>
        <h2 class="font-semibold text-charcoal-900 pt-2">How We Use Your Information</h2>
        <p>We use your information to fulfill orders, communicate order status, and improve our products and services. We do not sell your personal information to third parties.</p>
        <h2 class="font-semibold text-charcoal-900 pt-2">Data Security</h2>
        <p>Passwords are stored using industry-standard hashing. We take reasonable technical measures to protect your data from unauthorized access.</p>
        <h2 class="font-semibold text-charcoal-900 pt-2">Contact</h2>
        <p>For questions about this policy, contact us via the <a href="{{ route('contact') }}" class="text-denim-600 hover:underline">Contact Us</a> page.</p>
        <p class="text-xs text-charcoal-400 pt-4">This is placeholder policy text for demonstration purposes and should be reviewed by a legal professional before real-world use.</p>
    </div>
</div>
@endsection
