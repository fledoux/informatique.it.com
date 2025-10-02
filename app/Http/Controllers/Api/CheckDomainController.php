<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\AllowDomainRegistration;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CheckDomainController extends Controller
{
    /**
     * Check if email domain exists in allow_domain_registrations
     */
    public function checkDomain(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email'
        ]);

        $email = $request->input('email');
        
        // Extraire le domaine de l'email
        $domain = substr(strrchr($email, "@"), 1);
        
        if (empty($domain)) {
            return response()->json([
                'found' => false
            ]);
        }

        // Chercher si le domaine existe
        $allowedDomain = AllowDomainRegistration::where('domain', $domain)->exists();

        return response()->json([
            'found' => $allowedDomain
        ]);
    }
}
