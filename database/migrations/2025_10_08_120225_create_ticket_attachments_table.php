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
        Schema::create('ticket_attachments', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('active'); // active, inactive
            
            // Relations
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('message_id')->nullable()->constrained('ticket_messages')->onDelete('cascade');
            $table->foreignId('uploaded_by')->constrained('users')->onDelete('cascade');
            
            // Informations du fichier
            $table->string('s3_path', 255)->comment('Chemin complet S3: attachments/123/xxx.png');
            $table->string('original_filename', 190)->comment('Nom original du fichier');
            $table->string('mime_type', 100)->nullable();
            $table->bigInteger('size_bytes')->nullable()->comment('Taille en octets (max 20 Mo)');
            
            $table->timestamps();
            
            // Index pour performances
            $table->index('company_id');
            $table->index('ticket_id');
            $table->index('message_id');
            $table->index('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ticket_attachments');
    }
};
