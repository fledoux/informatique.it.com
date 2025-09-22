<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\Company;
use Spatie\Honeypot\ProtectAgainstSpam;

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
        // Get all dashboard statistics in optimized queries
        $ticketStats = Ticket::dashboardStats();
        $companiesCount = Company::count();
        $contactsCount = Contact::count();
        
        // Get the 20 most recent tickets
        $recentTickets = Ticket::recent(20);
        
        return view('pages.dashboard', compact(
            'ticketStats',
            'companiesCount', 
            'contactsCount',
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
    #[ProtectAgainstSpam]
    public function contact(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'type' => ['required', 'string', 'in:Individual,Association,Company,Collectivity'],
            'need' => ['required', 'string', 'max:5000'],
        ]);

        // Create the contact record
        Contact::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'type' => $validated['type'],
            'need' => $validated['need'],
        ]);

        // Redirect back with success message
        return redirect()->route('home')
            ->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons rapidement.');
    }
}