<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sso_audit_logs', function (Blueprint $table) {
            $table->id();
            $table->string('email');
            $table->enum('auth_method', ['sso', 'native'])->default('sso');
            $table->boolean('success')->default(false);
            $table->string('reason')->nullable();
            $table->ipAddress('ip_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();

            $table->index(['email', 'created_at']);
            $table->index(['success', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sso_audit_logs');
    }
};
