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
        Schema::create('appointment_bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_project_id')->constrained('appointment_projects')->onDelete('cascade');
            $table->date('appointment_date')->comment('Date du rendez-vous');
            $table->datetime('starts_at')->comment('Debut du creneau');
            $table->datetime('ends_at')->comment('Fin du creneau');
            $table->string('first_name', 100)->comment('Prenom du client');
            $table->string('last_name', 100)->comment('Nom du client');
            $table->string('email')->comment('Email du client');
            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();

            $table->unique(['appointment_project_id', 'starts_at'], 'appt_book_project_start_unique');
            $table->index(['appointment_project_id', 'appointment_date'], 'appt_book_project_date_idx');
            $table->index('email', 'appt_book_email_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_bookings');
    }
};
