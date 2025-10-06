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
     * Nettoie le contenu d'un email en supprimant tout ce qui suit le code de réponse
     *
     * @param string $emailContent
     * @return string
     */
    public static function cleanEmailContent(string $emailContent): string
    {
        // Trouver la position du code de réponse
        // Pattern pour ### TICKET_ID.UNIQUE_9_CHARS ###
        $pattern = '/' . preg_quote(self::CODE_PREFIX, '/') . '\d+\.[A-Z0-9]{' . self::UNIQUE_PART_LENGTH . '}' . preg_quote(self::CODE_SUFFIX, '/') . '/';
        
        if (preg_match($pattern, $emailContent, $matches, PREG_OFFSET_CAPTURE)) {
            // Couper le contenu avant le code de réponse
            $cleanContent = substr($emailContent, 0, $matches[0][1]);
            
            // Nettoyer les espaces et sauts de ligne en fin
            return trim($cleanContent);
        }
        
        // Si pas de code trouvé, retourner le contenu complet
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
     * @return string
     */
    public static function buildEmailWithReplyCode(string $messageContent, Ticket $ticket, ?string $existingCode = null): string
    {
        $replyCode = $existingCode ?? self::generateReplyCode($ticket);
        $formattedCode = self::formatCodeForEmail($replyCode);
        
        // Ajouter le code à la fin du message avec une séparation claire
        return $messageContent . "\n\n" . 
               "────────────────────────────────────────\n" .
               "Pour répondre à ce ticket, répondez directement à cet email.\n" .
               "Code de réponse : " . $formattedCode . "\n" .
               "Ne supprimez pas cette ligne lors de votre réponse.";
    }
}