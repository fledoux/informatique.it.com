<?php

namespace Tests\Feature;

use Tests\TestCase;

class FulllApiTest extends TestCase
{
    /**
     * Test de la page de test (dev uniquement)
     */
    public function test_fulll_test_page_accessible_in_local_environment(): void
    {
        $response = $this->get('/fulll/test/page');
        
        // En dev, la page doit être accessible
        if (app()->isLocal()) {
            $response->assertStatus(200);
            $response->assertViewIs('fulll.test');
        } else {
            // En production, il faut l'authentification
            $response->assertStatus(302); // Redirection vers login
        }
    }

    /**
     * Test de connexion à l'API (avec credentials)
     */
    public function test_fulll_connection_requires_authentication(): void
    {
        // Sans authentification
        $response = $this->get('/fulll/test/connection');
        $response->assertStatus(302); // Redirection vers login

        // Avec authentification
        $user = $this->createUser();
        $response = $this->actingAs($user)->get('/fulll/test/connection');
        $response->assertStatus(200);
    }

    /**
     * Test de création de client
     */
    public function test_fulll_create_client_requires_authentication(): void
    {
        $clientData = [
            'name' => 'Test Client',
            'email' => 'test@example.com',
            'phone' => '0123456789',
        ];

        // Sans authentification
        $response = $this->post('/fulll/test/clients', $clientData);
        $response->assertStatus(302);

        // Avec authentification
        $user = $this->createUser();
        $response = $this->actingAs($user)->post('/fulll/test/clients', $clientData);
        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
    }

    /**
     * Créer un utilisateur pour les tests
     */
    private function createUser()
    {
        return \App\Models\User::factory()->create([
            'company_id' => 1,
        ]);
    }
}
