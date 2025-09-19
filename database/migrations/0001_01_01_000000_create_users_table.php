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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');

            // Business fields
            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->unsignedBigInteger('company_id')->nullable()->index();

            // Profil
            $table->string('firstname', 120)->nullable();
            $table->string('lastname', 120)->nullable();
            $table->string('phone', 50)->nullable();

            // Activité / consentement
            $table->datetime('last_login')->nullable();
            $table->enum('agree_terms', ['oui', 'non'])->default('non');

            // Préférences & notes
            $table->json('channels')->nullable();     // {"email":true,"sms":true}
            $table->string('note', 255)->nullable();

            $table->rememberToken();
            $table->datetime('created_at')->nullable();
            $table->datetime('updated_at')->nullable();
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->datetime('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
