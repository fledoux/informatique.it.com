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
        Schema::create('conversation_shares', function (Blueprint $table) {
            $table->id();
            $table->string('status', 20)->default('active'); // active|inactive|expired
            $table->foreignId('ticket_id')->constrained()->onDelete('cascade');
            $table->foreignId('company_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('uuid', 36)->unique(); // UUID v4 pour accès public
            $table->timestamp('expires'); // Date d'expiration
            $table->timestamps();
            
            $table->index(['uuid', 'status']);
            $table->index(['ticket_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('conversation_shares');
    }
};
