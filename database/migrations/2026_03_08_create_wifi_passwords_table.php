<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wifi_passwords', function (Blueprint $table) {
            $table->id();
            $table->string('network_name')->default('test');
            $table->text('password'); // Chiffré automatiquement via le model
            $table->timestamp('changed_at')->useCurrent();
            $table->timestamps();
            
            $table->index('network_name');
            $table->index('changed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wifi_passwords');
    }
};
