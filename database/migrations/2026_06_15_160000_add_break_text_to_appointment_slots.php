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
        Schema::table('appointment_slots', function (Blueprint $table) {
            if (!Schema::hasColumn('appointment_slots', 'break_text')) {
                $table->string('break_text')->nullable()->after('slot_time')
                    ->comment('Texte personnalisé pour un créneau de pause (ex: "Pause déj", "Non disponible")');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_slots', function (Blueprint $table) {
            if (Schema::hasColumn('appointment_slots', 'break_text')) {
                $table->dropColumn('break_text');
            }
        });
    }
};
