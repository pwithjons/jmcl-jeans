<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class PageController extends Controller
{
    public function about(): View
    {
        return view('pages.about');
    }

    public function contact(): View
    {
        return view('pages.contact');
    }

    public function submitContact(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'message' => ['required', 'string', 'max:2000'],
        ]);

        // No dedicated contact_messages table in the schema (this is a
        // simple contact form, not a support ticket system), so the
        // message is logged for the store owner to pick up. Swap this
        // for Mail::send(...) once a free SMTP provider is configured in
        // .env — see MAIL_MAILER in .env.example.
        Log::info('Contact form submission', $validated);

        return back()->with('status', 'Thanks for reaching out — we will get back to you soon.');
    }

    public function privacy(): View
    {
        return view('pages.privacy');
    }

    public function terms(): View
    {
        return view('pages.terms');
    }
}
