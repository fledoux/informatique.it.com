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
        Schema::create('appointment_slots', function (Blueprint $table) {
            $table->id();
            $table->foreignId('appointment_project_id')->constrained('appointment_projects')->onDelete('cascade');
            $table->date('slot_date')->comment('Jour du creneau manuel');
            $table->time('slot_time')->comment('Heure de debut du creneau manuel');
            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();

            $table->unique(['appointment_project_id', 'slot_date', 'slot_time'], 'appt_slot_unique');
            $table->index(['appointment_project_id', 'slot_date'], 'appt_slot_project_date_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('appointment_slots');
    }
};
