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
        Schema::table('appointment_projects', function (Blueprint $table) {
            if (!Schema::hasColumn('appointment_projects', 'show_booking_names')) {
                $table->boolean('show_booking_names')->default(false)->after('is_active');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_projects', function (Blueprint $table) {
            $table->dropColumn('show_booking_names');
        });
    }
};
