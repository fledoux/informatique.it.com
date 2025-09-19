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
        Schema::create('contacts', function (Blueprint $table) {
            $table->id();
            $table->string('name')->comment('Nom du contact');
            $table->string('email')->comment('Email du contact');
            $table->string('phone', 50)->nullable()->comment('Téléphone du contact');
            $table->string('type')->comment('Type de demande');
            $table->longText('need')->comment('Description du besoin');
            $table->timestamps();
            
            // Index pour les recherches fréquentes
            $table->index('email');
            $table->index('type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contacts');
    }
};
