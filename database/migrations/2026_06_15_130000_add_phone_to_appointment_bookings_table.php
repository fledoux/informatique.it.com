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
        Schema::table('appointment_bookings', function (Blueprint $table) {
            $table->string('phone', 50)->nullable()->after('email')->comment('Telephone du client');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('appointment_bookings', function (Blueprint $table) {
            $table->dropColumn('phone');
        });
    }
};
