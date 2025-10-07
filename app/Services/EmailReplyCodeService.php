<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class EmailReplyCodeService
{
    /**
     * Longueur de la partie unique (9 caractères)
     */
    private const UNIQUE_PART_LENGTH = 9;
    
    /**
     * Préfixe et suffixe pour identifier les codes
     */
    private const CODE_PREFIX = '### ';
    private const CODE_SUFFIX = ' ###';
    
    /**
     * Génère un code de réponse unique pour un ticket
     *
     * @param Ticket $ticket
     * @return string
     */
    public static function generateReplyCode(Ticket $ticket): string
    {
        // Format: TICKET_ID.UNIQUE_9_CHARS
        // Ex: 15.KD8BSBS35, 142.ABC123XYZ
        $ticketId = $ticket->id;
        $uniquePart = strtoupper(Str::random(self::UNIQUE_PART_LENGTH));
        
        return $ticketId . '.' . $uniquePart;
    }
    
    /**
     * Formate le code avec les balises pour insertion dans l'email
     *
     * @param string $code
     * @return string
     */
    public static function formatCodeForEmail(string $code): string
    {
        return self::CODE_PREFIX . $code . self::CODE_SUFFIX;
    }
    
    /**
     * Extrait le code de réponse depuis le contenu d'un email
     *
     * @param string $emailContent
     * @return string|null
     */
    public static function extractReplyCode(string $emailContent): ?string
    {
        // Pattern pour matcher ### TICKET_ID.UNIQUE_9_CHARS ###
        // Ex: ### 15.KD8BSBS35 ###
        $pattern = '/' . preg_quote(self::CODE_PREFIX, '/') . '(\d+\.[A-Z0-9]{' . self::UNIQUE_PART_LENGTH . '})' . preg_quote(self::CODE_SUFFIX, '/') . '/';
        
        if (preg_match($pattern, $emailContent, $matches)) {
            return $matches[1];
        }
        
        return null;
    }
    
    /**
     * Récupère le ticket associé à un code de réponse
     *
     * @param string $code
     * @return Ticket|null
     */
    public static function getTicketFromReplyCode(string $code): ?Ticket
    {
        // Extraire l'ID du ticket avant le point
        // Ex: "15.KD8BSBS35" -> 15
        $parts = explode('.', $code);
        
        if (count($parts) !== 2) {
            Log::warning('EmailReplyCode - Invalid code format', ['code' => $code]);
            return null;
        }
        
        $ticketId = (int) $parts[0];
        
        // Vérifier que le ticket existe
        $ticket = Ticket::find($ticketId);
        
        if (!$ticket) {
            Log::warning('EmailReplyCode - Ticket not found', [
                'code' => $code,
                'extracted_ticket_id' => $ticketId
            ]);
            return null;
        }
        
        Log::info('EmailReplyCode - Ticket found', [
            'code' => $code,
            'ticket_id' => $ticket->id,
            'ticket_subject' => $ticket->subject
        ]);
        
        return $ticket;
    }
    
    /**
     * Nettoie le contenu d'un email en supprimant tout ce qui suit la ligne de séparation
     *
     * @param string $emailContent
     * @return string
     */
    public static function cleanEmailContent(string $emailContent): string
    {
        // Chercher d'abord la ligne de séparation principale
        $separators = [
            '##### METTEZ VOTRE RÉPONSE AU-DESSUS DE CETTE LIGNE #####',
            '────────────────────────────────────────', // Ancienne séparation (compatibilité)
        ];
        
        foreach ($separators as $separator) {
            $position = strpos($emailContent, $separator);
            if ($position !== false) {
                // Couper le contenu avant la ligne de séparation
                $cleanContent = substr($emailContent, 0, $position);
                return trim($cleanContent);
            }
        }
        
        // Si aucune séparation trouvée, chercher le code de réponse
        $pattern = '/' . preg_quote(self::CODE_PREFIX, '/') . '\d+\.[A-Z0-9]{' . self::UNIQUE_PART_LENGTH . '}' . preg_quote(self::CODE_SUFFIX, '/') . '/';
        
        if (preg_match($pattern, $emailContent, $matches, PREG_OFFSET_CAPTURE)) {
            $cleanContent = substr($emailContent, 0, $matches[0][1]);
            return trim($cleanContent);
        }
        
        // Si rien trouvé, retourner le contenu complet
        return trim($emailContent);
    }
    
    /**
     * Valide qu'un code de réponse a le bon format
     *
     * @param string $code
     * @return bool
     */
    public static function isValidReplyCode(string $code): bool
    {
        // Format: TICKET_ID.UNIQUE_9_CHARS
        // Ex: 15.KD8BSBS35, 142.ABC123XYZ
        $parts = explode('.', $code);
        
        if (count($parts) !== 2) {
            return false;
        }
        
        $ticketId = $parts[0];
        $uniquePart = $parts[1];
        
        // Vérifier que l'ID du ticket est numérique
        if (!ctype_digit($ticketId)) {
            return false;
        }
        
        // Vérifier que la partie unique a la bonne longueur et format
        return strlen($uniquePart) === self::UNIQUE_PART_LENGTH && 
               ctype_alnum($uniquePart) && 
               $uniquePart === strtoupper($uniquePart);
    }
    
    /**
     * Génère le contenu complet d'email avec le code de réponse
     *
     * @param string $messageContent
     * @param Ticket $ticket
     * @param string|null $existingCode Code existant à utiliser (optionnel)
     * @param bool $includeInstructions Inclure les instructions (pour emails seulement)
     * @return array Format: ['content' => string, 'header' => string|null]
     */
    public static function buildEmailWithReplyCode(string $messageContent, Ticket $ticket, ?string $existingCode = null, bool $includeInstructions = true): array
    {
        $replyCode = $existingCode ?? self::generateReplyCode($ticket);
        $formattedCode = self::formatCodeForEmail($replyCode);
        
        if ($includeInstructions) {
            // Pour les emails : construire un header HTML complet
            $header = '<div style="max-width: 600px; margin: 10px auto; text-align: center; font-family: Arial, Helvetica, sans-serif;">' .
                     '<div style="font-size: 16px; margin-bottom: 10px;">' .
                     '##### METTEZ VOTRE RÉPONSE AU-DESSUS DE CETTE LIGNE #####' .
                     '</div>' .
                     '<div style="font-size: 12px;">' .
                     'Pour répondre à ce ticket, répondez directement à cet email.<br>' .
                     'Code de réponse : ' . $formattedCode . '<br>' .
                     'Ne supprimez pas cette ligne lors de votre réponse.' .
                     '</div>' .
                     '</div>';
            
            return [
                'content' => $messageContent,
                'header' => $header
            ];
        } else {
            // Pour les tickets web : juste le contenu
            return [
                'content' => $messageContent,
                'header' => null
            ];
        }
    }
}