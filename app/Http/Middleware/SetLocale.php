<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SetLocale
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Langues supportées
        $supportedLocales = ['fr', 'en'];
        $defaultLocale = 'fr';
        
        // Déterminer la locale : cookie -> session -> header navigateur -> défaut
        $locale = $request->cookie('locale') 
                 ?? $request->session()->get('locale')
                 ?? $this->parseAcceptLanguage($request->header('Accept-Language'), $supportedLocales)
                 ?? $defaultLocale;
        
        // Valider que la locale est supportée
        if (!in_array($locale, $supportedLocales)) {
            $locale = $defaultLocale;
        }
        
        // Appliquer la locale
        app()->setLocale($locale);
        
        // Sauvegarder en session pour la requête courante
        $request->session()->put('locale', $locale);
        
        return $next($request);
    }
    
    /**
     * Parse Accept-Language header pour trouver la meilleure correspondance
     */
    private function parseAcceptLanguage(?string $acceptLanguage, array $supportedLocales): ?string
    {
        if (!$acceptLanguage) {
            return null;
        }
        
        // Parser le header Accept-Language (format: fr-FR,fr;q=0.9,en;q=0.8)
        $languages = [];
        preg_match_all('/([a-z]{2})(?:-[A-Z]{2})?(?:;q=([0-9.]+))?/', $acceptLanguage, $matches, PREG_SET_ORDER);
        
        foreach ($matches as $match) {
            $lang = $match[1];
            $quality = isset($match[2]) ? (float) $match[2] : 1.0;
            $languages[$lang] = $quality;
        }
        
        // Trier par qualité décroissante
        arsort($languages);
        
        // Trouver la première langue supportée
        foreach ($languages as $lang => $quality) {
            if (in_array($lang, $supportedLocales)) {
                return $lang;
            }
        }
        
        return null;
    }
}
