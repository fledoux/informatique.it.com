<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\Company;
use App\Services\PushoverService;
use Spatie\Honeypot\ProtectAgainstSpam;
use Illuminate\Support\Facades\Mail;

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

    public function qr(Request $request)
    {
        Log::info('QR Route accessed', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method()
        ]);

        try {
            // Send Pushover notification when the page is accessed
            $this->sendPushoverNotification();
            Log::info('Pushover notification sent successfully for QR scan');
        } catch (\Exception $e) {
            Log::error('Pushover notification failed for QR scan', ['error' => $e->getMessage()]);
        }

        try {
            //Mail::to('fledoux@yellowcactus.com')->send(new \App\Mail\globalMail('Scan QR Code', $request->ip()));
            Log::info('Email sent successfully for QR scan');
        } catch (\Exception $e) {
            Log::error('Email failed for QR scan', ['error' => $e->getMessage()]);
        }

        return redirect()->route('home')->with('success', 'Merci d\'avoir scanné notre QR code !');
    }

    public function qrCode()
    {
        

        // Génère le QR code pour l'URL https://informatique.it.com
        $url = 'https://informatique.it.com/qr';
        $dataUri = null;
        try {
            $options = new \chillerlan\QRCode\QROptions([
                'version'         => 4,
                'outputInterface' => \chillerlan\QRCode\Output\QRMarkupSVG::class,
                'eccLevel'        => \chillerlan\QRCode\Common\EccLevel::M,
                'addQuietzone'    => true,
                'quietzoneSize'   => 2,
                'svgViewBoxSize'  => 512,
                'markupDark'      => '#000000',
                'markupLight'     => '#ffffff',
                'svgOpacity'      => 1.0,
            ]);
            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $svgContent = $qrcode->render($url);
            
            // Nettoyer le SVG pour l'affichage direct
            $dataUri = $svgContent;
        } catch (\Exception $e) {
            $dataUri = null;
            Log::error('QR code generation error: ' . $e->getMessage());
        }

        return view('pages.qr-code', compact('dataUri'));
    }

    private function sendPushoverNotification()
    {
        $title = 'QR Code scanné';
        $message = 'Depuis ' . request()->ip() . ' à ' . now()->format('H:i:s');
        
        PushoverService::send($title, $message);
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

        // Redirect back with success message
        return redirect()->route('home')
            ->with('success', 'Votre message a été envoyé avec succès. Nous vous répondrons rapidement.');
    }
}