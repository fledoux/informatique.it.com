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
        Schema::create('tickets', function (Blueprint $table) {
            $table->id();
            
            // Enum fields MySQL natifs
            $table->enum('status', ['new', 'in_progress', 'waiting', 'resolved', 'closed', 'canceled'])->default('new');
            $table->enum('priority', ['low', 'normal', 'high', 'urgent'])->default('normal');
            
            // Relations vers Company et User
            $table->foreignId('company_id')->constrained('companies')->onDelete('cascade');
            $table->foreignId('author_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('assigned_to')->nullable()->constrained('users')->onDelete('set null');
            
            // Dates et timestamps
            $table->datetime('assigned_at')->nullable()->comment('Date/heure de prise en charge');
            $table->datetime('due')->nullable()->comment('Livraison au plus tard');
            
            // Champs texte
            $table->string('folder_code', 64)->nullable()->comment('Code dossier ou référence interne');
            $table->string('subject', 190)->comment('Sujet court du ticket');
            $table->text('question')->nullable()->comment('Description initiale');
            
            // Facturation
            $table->boolean('billable')->default(true)->comment('Ticket facturable');
            
            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();
            
            // Index pour les recherches fréquentes
            $table->index(['company_id', 'status']);
            $table->index(['assigned_to', 'status']);
            $table->index('due');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tickets');
    }
};
