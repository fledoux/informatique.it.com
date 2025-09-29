<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Services\PushoverService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class Page extends Model
{
    /**
     * Envoie une notification Pushover pour le scan QR
     */
    public static function sendQrScanNotification(string $title = 'QR Code scanné'): void
    {
        $message = 'IP : ' . request()->ip() . "\n" . 'Date : ' . now()->format('d/m/Y') . "\n" . 'Heure : ' . now()->format('H:i:s');
        PushoverService::send($title, $message);
    }

    public static function scan($request, string $title = 'QR Code scanné'): void
    {
        Log::info('QR Route accessed', [
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'method' => $request->method()
        ]);

        try {
            // Send Pushover notification when the page is accessed
            Page::sendQrScanNotification('SCAN ' . $title);
            Log::info('Pushover notification sent successfully for QR scan');
        } catch (\Exception $e) {
            Log::error('Pushover notification failed for QR scan', ['error' => $e->getMessage()]);
        }

        try {
            Mail::to('fledoux@yellowcactus.com')->send(new \App\Mail\globalMail('SCAN ' . $title, $request->ip()));
            Log::info('Email sent successfully for QR scan');
        } catch (\Exception $e) {
            Log::error('Email failed for QR scan', ['error' => $e->getMessage()]);
        }
    }

    public static function generateQrCode(string $url): ?string
    {
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
        return $dataUri;
    }
}
