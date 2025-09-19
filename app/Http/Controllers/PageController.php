<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact;

class PageController extends Controller
{
    /**
     * Show the home page
     */
    public function home()
    {
        // If user is authenticated, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }
        
        return view('pages.home');
    }

    /**
     * Show the dashboard (requires authentication)
     */
    public function dashboard()
    {
        // This will be handled by middleware
        // For now, basic implementation
        
        $now = now();
        
        // Mock data - you'll need to implement actual repositories/models
        $ticketsCount = 0;
        $companiesCount = 0;
        $contactsCount = 0;
        $openTicketsCount = 0;
        $waitingCount = 0;
        $overdueCount = 0;
        $recentTickets = collect();
        
        return view('pages.dashboard', compact(
            'ticketsCount',
            'companiesCount', 
            'contactsCount',
            'openTicketsCount',
            'waitingCount',
            'overdueCount',
            'recentTickets'
        ));
    }

    /**
     * Show the legal page
     */
    public function legal()
    {
        return view('pages.legal');
    }

    /**
     * Show the RGPD/privacy policy page
     */
    public function rgpd()
    {
        return view('pages.rgpd');
    }

    /**
     * Show the terms of service page
     */
    public function cgv()
    {
        return view('pages.cgv');
    }

    /**
     * Handle contact form submission
     */
    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'company' => ['nullable', 'string', 'max:255'],
            'subject' => ['required', 'string', 'in:devis,info,urgence,partenariat'],
            'message' => ['required', 'string', 'max:5000'],
        ]);

        // Create the contact record
        Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => null, // Not in the form, could be added later
            'type' => $validated['subject'],
            'need' => "Entreprise: " . ($validated['company'] ?? 'Non précisée') . "\n\n" . $validated['message'],
        ]);

        // Redirect back with success message
        return redirect()->route('home')
            ->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons rapidement.');
    }
}