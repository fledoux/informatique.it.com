<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\Company;
use App\Models\Page;
use Spatie\Honeypot\ProtectAgainstSpam;
use App\Helpers\Helper;

class PageController extends Controller
{
    public function home()
    {
        // If user is authenticated, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        return view('pages.home');
    }

    public function dashboard()
    {
        // Get all dashboard statistics in optimized queries
        $ticketStats = Ticket::dashboardStats();
        $companiesCount = Company::count();
        $contactsCount = Contact::count();
        $lastXTickets = 20;

        // Get the 20 most recent tickets
        $recentTickets = Ticket::getMyLastTickets($lastXTickets);

        return view('pages.dashboard', compact(
            'ticketStats',
            'companiesCount',
            'contactsCount',
            'recentTickets',
            'lastXTickets'
        ));
    }

    public function legal()
    {
        return view('pages.legal');
    }

    public function rgpd()
    {
        return view('pages.rgpd');
    }

    public function cgv()
    {
        return view('pages.cgv');
    }

    public function cgu()
    {
        return view('pages.cgu');
    }

    public function web(Request $request)
    {
        Page::scan($request, 'WEB');
        return redirect()->route('home')->with('success', 'Merci d\'avoir scanné notre QR code !');
    }

    public function flyer(Request $request)
    {
        Page::scan($request, 'FLYER');
        return redirect()->route('home')->with('success', 'Merci d\'avoir scanné notre QR code !');
    }

    public function street(Request $request)
    {
        Page::scan($request, 'RUE');
        return redirect()->route('home')->with('success', 'Merci d\'avoir scanné notre QR code !');
    }

    public function car(Request $request)
    {
        Page::scan($request, 'CAR');
        return redirect()->route('home')->with('success', 'Merci d\'avoir scanné notre QR code !');
    }

    public function qrCode()
    {
        $dataUri = Page::generateQrCode(config('app.company.url') . '/web');
        return view('pages.qr-code', compact('dataUri'));
    }

    #[ProtectAgainstSpam]
    public function contact(Request $request)
    {

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255'],
            'phone' => ['required', 'string', 'max:20'],
            'type' => ['required', 'string', 'in:particulier,entreprise,association,autre'],
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

        $title = 'Nouveau Contact';
        $message = $validated['name'] . "\n" . $validated['email'] . "\n" . $validated['phone'] . "\n" . $validated['type'] . "\n" . $validated['need'];
        Helper::sendPushoverNotification($title, $message);

        // Redirect back with success message
        return redirect()->route('home')
            ->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons rapidement.');
    }
}
