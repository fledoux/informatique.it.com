<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateSSOCertificates extends Command
{
    protected $signature = 'sso:generate-certificates';
    protected $description = 'Generate self-signed certificates for SSO Service Provider';

    public function handle()
    {
        $ssoPath = storage_path('app/sso');
        $certPath = "{$ssoPath}/sp.crt";
        $keyPath = "{$ssoPath}/sp.key";

        // Create directory if it doesn't exist
        if (!is_dir($ssoPath)) {
            mkdir($ssoPath, 0755, true);
        }

        // Check if certificates already exist
        if (file_exists($certPath) && file_exists($keyPath)) {
            $this->info('✅ Certificates already exist.');
            return Command::SUCCESS;
        }

        $this->info('Generating self-signed certificates for SSO...');

        // Generate private key
        $privateKey = openssl_pkey_new([
            'private_key_bits' => 2048,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);

        if (!$privateKey) {
            $this->error('❌ Failed to generate private key');
            return Command::FAILURE;
        }

        // Generate certificate for 1 year
        $csr = openssl_csr_new(
            [
                'CN' => config('app.url'),
                'O' => config('app.name'),
            ],
            $privateKey,
            ['digest_alg' => 'sha256']
        );

        if (!$csr) {
            $this->error('❌ Failed to generate certificate signing request');
            return Command::FAILURE;
        }

        $cert = openssl_csr_sign(
            $csr,
            null,
            $privateKey,
            365,
            ['digest_alg' => 'sha256']
        );

        if (!$cert) {
            $this->error('❌ Failed to generate certificate');
            return Command::FAILURE;
        }

        // Export private key
        openssl_pkey_export($privateKey, $keyContent);
        file_put_contents($keyPath, $keyContent);
        chmod($keyPath, 0600);

        // Export certificate
        openssl_x509_export($cert, $certContent);
        file_put_contents($certPath, $certContent);

        $this->info('✅ Certificates generated successfully!');
        $this->info("📁 Certificate: {$certPath}");
        $this->info("🔑 Private Key: {$keyPath}");

        return Command::SUCCESS;
    }
}
