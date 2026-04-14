<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;

class CacheOidcConfig extends Command
{
    protected $signature = 'oidc:cache-config';
    protected $description = 'Pre-fetch and cache OIDC configuration to avoid timeout on first request';

    public function handle()
    {
        $baseUrl = config('oauth.base_url');
        
        if (!$baseUrl) {
            $this->error('OIDC_BASE_URL not configured');
            return 1;
        }

        try {
            $client = new Client([
                'timeout' => 10,
                'connect_timeout' => 5,
                'verify' => false,
            ]);

            $configUrl = rtrim($baseUrl, '/') . '/.well-known/openid-configuration';
            
            $this->info("Fetching OIDC config from: {$configUrl}");
            
            $response = $client->get($configUrl);
            $config = json_decode($response->getBody(), true);

            // Cache the entire config for 24 hours
            cache()->put('oidc_well_known_config', $config, now()->addHours(24));

            $this->info('✓ OIDC configuration cached successfully');
            $this->info('Cached endpoints:');
            $this->info('  - authorization_endpoint: ' . ($config['authorization_endpoint'] ?? 'N/A'));
            $this->info('  - token_endpoint: ' . ($config['token_endpoint'] ?? 'N/A'));
            $this->info('  - userinfo_endpoint: ' . ($config['userinfo_endpoint'] ?? 'N/A'));

            return 0;

        } catch (\Exception $e) {
            $this->error('Failed to fetch OIDC config: ' . $e->getMessage());
            return 1;
        }
    }
}
