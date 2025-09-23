<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class LocaleController extends Controller
{
    /**
     * Changer la langue de l'utilisateur
     */
    public function change(Request $request)
    {
        $locale = $request->input('locale');
        $supportedLocales = ['fr', 'en'];
        
        // Valider la locale
        if (!in_array($locale, $supportedLocales)) {
            return response()->json([
                'success' => false,
                'message' => 'Langue non supportée'
            ], 400);
        }
        
        // Sauvegarder en session
        $request->session()->put('locale', $locale);
        
        // Créer la réponse avec cookie
        $response = response()->json([
            'success' => true,
            'locale' => $locale,
            'message' => __('global.messages.locale_updated')
        ]);
        
        // Définir le cookie (expire dans 1 an)
        $response->cookie('locale', $locale, 60 * 24 * 365); // 365 jours
        
        return $response;
    }
    
    /**
     * Obtenir la locale courante
     */
    public function current()
    {
        return response()->json([
            'locale' => app()->getLocale(),
            'supported_locales' => ['fr', 'en']
        ]);
    }
}
