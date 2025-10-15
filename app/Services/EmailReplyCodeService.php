<?php

namespace App\Services;

use App\Models\Ticket;
use App\Models\TicketReplyCode;
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
     * Génère un code de réponse unique pour un ticket et le stocke en BDD
     *
     * @param Ticket $ticket
     * @param string $recipientEmail
     * @return string
     */
    public static function generateReplyCode(Ticket $ticket, string $recipientEmail): string
    {
        // Format: TICKET_ID.UNIQUE_9_CHARS
        // Ex: 15.KD8BSBS35, 142.ABC123XYZ
        $ticketId = $ticket->id;
        $uniquePart = strtoupper(Str::random(self::UNIQUE_PART_LENGTH));
        $code = $ticketId . '.' . $uniquePart;
        
        // Stocker le code en BDD avec expiration configurable
        $expirationDays = (int) config('mail.reply_code_expiration_days', 30);
        $expiresAt = now()->addDays($expirationDays);
        
        TicketReplyCode::create([
            'ticket_id' => $ticket->id,
            'code' => $code,
            'recipient_email' => $recipientEmail,
            'expires_at' => $expiresAt,
        ]);
        
        Log::info('EmailReplyCode - Code generated and stored', [
            'ticket_id' => $ticket->id,
            'code' => $code,
            'recipient_email' => $recipientEmail,
            'expires_at' => $expiresAt->toDateTimeString(),
            'expiration_days' => $expirationDays,
        ]);
        
        return $code;
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
     * Récupère le ticket associé à un code de réponse avec validation sécurisée
     *
     * @param string $code
     * @param string $senderEmail Email de l'expéditeur pour validation
     * @return Ticket|null
     */
    public static function getTicketFromReplyCode(string $code, string $senderEmail): ?Ticket
    {
        // Vérifier que le code existe en BDD
        $replyCode = TicketReplyCode::where('code', $code)
            ->valid()
            ->first();
        
        if (!$replyCode) {
            Log::warning('EmailReplyCode - Code not found or expired', [
                'code' => $code,
                'sender_email' => $senderEmail
            ]);
            return null;
        }
        
        // SÉCURITÉ : Vérifier que l'email de l'expéditeur correspond au destinataire du code
        if ($replyCode->recipient_email !== $senderEmail) {
            Log::warning('EmailReplyCode - Email mismatch', [
                'code' => $code,
                'expected_email' => $replyCode->recipient_email,
                'sender_email' => $senderEmail
            ]);
            return null;
        }
        
        // Marquer le code comme utilisé (première utilisation)
        $replyCode->markAsUsed();
        
        Log::info('EmailReplyCode - Valid code, ticket found', [
            'code' => $code,
            'ticket_id' => $replyCode->ticket_id,
            'sender_email' => $senderEmail
        ]);
        
        return $replyCode->ticket;
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
     * @param string $recipientEmail Email du destinataire pour générer le code
     * @param string|null $existingCode Code existant à utiliser (optionnel)
     * @param bool $includeInstructions Inclure les instructions (pour emails seulement)
     * @return array Format: ['content' => string, 'header' => string|null]
     */
    public static function buildEmailWithReplyCode(string $messageContent, Ticket $ticket, string $recipientEmail, ?string $existingCode = null, bool $includeInstructions = true): array
    {
        $replyCode = $existingCode ?? self::generateReplyCode($ticket, $recipientEmail);
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
                     'Ne supprimez pas ces lignes lors de votre réponse.' .
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