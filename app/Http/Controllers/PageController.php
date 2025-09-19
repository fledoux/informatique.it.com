<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
}