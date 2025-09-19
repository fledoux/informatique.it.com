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
        Schema::create('companies', function (Blueprint $table) {
            $table->id();
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->string('name', 190);
            $table->string('siret', 20)->nullable();
            $table->string('vat_number', 32)->nullable();
            $table->string('email', 190)->nullable();
            $table->string('phone', 50)->nullable();
            $table->string('website', 190)->nullable();
            $table->string('address_line1', 190)->nullable();
            $table->string('address_line2', 190)->nullable();
            $table->string('zip', 20)->nullable();
            $table->string('city', 120)->nullable();
            $table->string('country', 2)->nullable();
            $table->text('notes')->nullable();
            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('companies');
    }
};
