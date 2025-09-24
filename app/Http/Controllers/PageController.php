<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;
use App\Models\Contact;
use App\Models\Ticket;
use App\Models\Company;
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
        $recentTickets = Ticket::recent($lastXTickets);

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
        Mail::to('fledoux@yellowcactus.com')->send(new \App\Mail\globalMail('Scan QR Code', $request->ip() ));
        return redirect()->route('home');
    }

    public function qrCode()
    {
        // Send Pushover notification when the page is accessed
        $this->sendPushoverNotification();

        // Génère le QR code pour l'URL https://informatique.it.com
        $url = 'https://informatique.it.com/qr';
        $dataUri = null;
        try {
            $options = new \chillerlan\QRCode\QROptions([
                'version'      => 5,
                'outputType'   => \chillerlan\QRCode\QRCode::OUTPUT_IMAGE_PNG,
                'eccLevel'     => \chillerlan\QRCode\QRCode::ECC_M,
                'scale'        => 8,
                'imageBase64'  => true,
                'addQuietzone' => true,
                'quietzoneSize'=> 2,
            ]);
            $qrcode = new \chillerlan\QRCode\QRCode($options);
            $dataUri = $qrcode->render($url);
        } catch (\Exception $e) {
            $dataUri = null;
            Log::error('QR code generation error: ' . $e->getMessage());
        }

        return view('pages.qr-code', compact('dataUri'));
    }

    private function sendPushoverNotification()
    {
        // Configuration Pushover - vous devez définir ces valeurs dans votre .env
        $token = config('services.pushover.token'); // APP_TOKEN
        $user = config('services.pushover.user');   // USER_KEY
        
        if (!$token || !$user) {
            Log::info('Pushover not configured - QR code page accessed');
            return;
        }

        $data = [
            'token' => $token,
            'user' => $user,
            'title' => 'QR Code scanné',
            'message' => 'Quelqu\'un a scanné le QR code depuis ' . request()->ip() . ' à ' . now()->format('H:i:s'),
            'priority' => 0,
        ];

        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.pushover.net/1/messages.json');
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
            curl_setopt($ch, CURLOPT_SAFE_UPLOAD, true);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $result = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            curl_close($ch);
            
            if ($httpCode === 200) {
                Log::info('Pushover notification sent successfully');
            } else {
                Log::warning('Pushover notification failed', ['http_code' => $httpCode, 'response' => $result]);
            }
        } catch (\Exception $e) {
            Log::error('Pushover notification error: ' . $e->getMessage());
        }
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