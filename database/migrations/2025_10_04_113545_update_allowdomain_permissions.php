<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Mettre à jour les permissions allow-domain-registration vers allowdomain
        $oldPermissions = [
            'allow-domain-registration.index',
            'allow-domain-registration.show',
            'allow-domain-registration.create',
            'allow-domain-registration.edit',
            'allow-domain-registration.delete'
        ];
        
        $newPermissions = [
            'allowdomain.index',
            'allowdomain.show',
            'allowdomain.create',
            'allowdomain.edit',
            'allowdomain.delete'
        ];

        for ($i = 0; $i < count($oldPermissions); $i++) {
            DB::table('permissions')
                ->where('name', $oldPermissions[$i])
                ->update(['name' => $newPermissions[$i]]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remettre les anciennes permissions
        $newPermissions = [
            'allowdomain.index',
            'allowdomain.show',
            'allowdomain.create',
            'allowdomain.edit',
            'allowdomain.delete'
        ];
        
        $oldPermissions = [
            'allow-domain-registration.index',
            'allow-domain-registration.show',
            'allow-domain-registration.create',
            'allow-domain-registration.edit',
            'allow-domain-registration.delete'
        ];

        for ($i = 0; $i < count($newPermissions); $i++) {
            DB::table('permissions')
                ->where('name', $newPermissions[$i])
                ->update(['name' => $oldPermissions[$i]]);
        }
    }
};
