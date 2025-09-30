<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('allow_domain_registrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->onDelete('cascade')->index();
            $table->string('domain', 255);
            $table->timestamps();

            // Index unique pour éviter les doublons domain/company
            $table->unique(['company_id', 'domain']);
            
            // Index sur domain pour les recherches rapides
            $table->index('domain');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('allow_domain_registrations');
    }
};
