<?php

namespace App\Http\Controllers;

use App\Services\ImapService;
use Illuminate\Support\Facades\Log;

class ImapController extends Controller
{
    private ImapService $imapService;

    public function __construct(ImapService $imapService)
    {
        $this->middleware('permission:ticket.create');
        $this->imapService = $imapService;
    }

    /**
     * Affiche la page de gestion IMAP
     */
    public function index()
    {
        $config = [
            'host' => config('imap.host'),
            'port' => config('imap.port'),
            'username' => config('imap.username'),
            'encryption' => config('imap.encryption'),
            'folder' => config('imap.folder'),
        ];

        return view('admin.imap.index', compact('config'));
    }

    /**
     * Test de connexion IMAP
     */
    public function testConnection()
    {
        try {
            $result = $this->imapService->testConnection();

            if ($result['success']) {
                return response()->json([
                    'success' => true,
                    'message' => $result['message'],
                    'details' => $result['details']
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => $result['message']
                ], 422);
            }

        } catch (\Exception $e) {
            Log::error('IMAP test connection error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors du test de connexion: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Récupère manuellement les emails
     */
    public function fetchEmails()
    {
        try {
            if (!$this->imapService->connect()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Impossible de se connecter au serveur IMAP'
                ], 422);
            }

            $processed = $this->imapService->fetchNewEmails();
            $this->imapService->disconnect();

            return response()->json([
                'success' => true,
                'message' => $processed > 0 
                    ? "{$processed} email(s) traité(s) avec succès"
                    : "Aucun nouvel email à traiter",
                'processed' => $processed
            ]);

        } catch (\Exception $e) {
            Log::error('IMAP fetch emails error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Erreur lors de la récupération des emails: ' . $e->getMessage()
            ], 500);
        }
    }
}