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
        Schema::create('ticket_messages', function (Blueprint $table) {
            $table->id();
            
            // Champs principaux
            $table->enum('status', ['active', 'inactive', 'internal'])->default('active');
            $table->string('subject', 190);
            $table->longText('body')->nullable();
            
            // Relations
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('ticket_id')->constrained('tickets')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            
            // Index pour performance
            $table->index('ticket_id');
            $table->index(['ticket_id', 'status']); // Pour filtrer par ticket et status
            $table->index('status');
            
            // Timestamps au format datetime
            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_messages');
    }
};
