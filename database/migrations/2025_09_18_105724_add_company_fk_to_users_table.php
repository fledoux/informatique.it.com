<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // S’assure que la colonne existe (au cas où) :
            if (!Schema::hasColumn('users', 'company_id')) {
                $table->unsignedBigInteger('company_id')->nullable()->index()->after('status');
            }

            // Ajoute la contrainte uniquement si elle n’existe pas déjà
            $table->foreign('company_id')
                ->references('id')->on('companies')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Drop FK si présente
            $table->dropForeign(['company_id']);

            // On laisse la colonne, mais tu peux la drop si tu préfères :
            // $table->dropColumn('company_id');
        });
    }
};
