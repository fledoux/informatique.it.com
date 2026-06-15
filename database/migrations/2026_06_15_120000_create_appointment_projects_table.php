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
        Schema::create('appointment_projects', function (Blueprint $table) {
            $table->id();
            $table->string('serial', 36)->unique()->comment('UUID generé automatiquement pour accès public');
            $table->string('title')->comment('Titre affiche sur la page publique');
            $table->text('subtitle')->nullable()->comment('Sous-titre affiche sur la page publique');
            $table->boolean('single_registration_per_person')->default(true)
                ->comment('Autorise une seule reservation par email pour ce projet');
            $table->unsignedTinyInteger('slot_duration_minutes')->default(30)
                ->comment('Duree d un creneau en minutes: 15, 30 ou 45');
            $table->time('day_start_time')->default('08:00:00')->comment('Heure de debut des disponibilites');
            $table->time('day_end_time')->default('17:00:00')->comment('Heure de fin des disponibilites');
            $table->unsignedSmallInteger('booking_horizon_days')->nullable()
                ->comment('Nombre de jours affiches a partir d aujourd hui (null = pas de limite stricte)');
            $table->boolean('is_active')->default(true)->comment('Rend la prise de rendez-vous publique active');
            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();

            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_projects');
    }
};
