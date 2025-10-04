<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // === PERMISSIONS AUTOMATIQUES ===
                // Permissions allowdomain
        Permission::firstOrCreate(['name' => 'allowdomain.index']);
        Permission::firstOrCreate(['name' => 'allowdomain.show']);
        Permission::firstOrCreate(['name' => 'allowdomain.create']);
        Permission::firstOrCreate(['name' => 'allowdomain.edit']);
        Permission::firstOrCreate(['name' => 'allowdomain.delete']);

        // Permissions company
        Permission::firstOrCreate(['name' => 'company.index']);
        Permission::firstOrCreate(['name' => 'company.show']);
        Permission::firstOrCreate(['name' => 'company.create']);
        Permission::firstOrCreate(['name' => 'company.edit']);
        Permission::firstOrCreate(['name' => 'company.delete']);

        // Permissions contact
        Permission::firstOrCreate(['name' => 'contact.index']);
        Permission::firstOrCreate(['name' => 'contact.show']);
        Permission::firstOrCreate(['name' => 'contact.create']);
        Permission::firstOrCreate(['name' => 'contact.edit']);
        Permission::firstOrCreate(['name' => 'contact.delete']);

        // Permissions locale
        Permission::firstOrCreate(['name' => 'locale.index']);
        Permission::firstOrCreate(['name' => 'locale.show']);
        Permission::firstOrCreate(['name' => 'locale.create']);
        Permission::firstOrCreate(['name' => 'locale.edit']);
        Permission::firstOrCreate(['name' => 'locale.delete']);

        // Permissions page
        Permission::firstOrCreate(['name' => 'page.index']);
        Permission::firstOrCreate(['name' => 'page.show']);
        Permission::firstOrCreate(['name' => 'page.create']);
        Permission::firstOrCreate(['name' => 'page.edit']);
        Permission::firstOrCreate(['name' => 'page.delete']);

        // Permissions permission
        Permission::firstOrCreate(['name' => 'permission.index']);
        Permission::firstOrCreate(['name' => 'permission.show']);
        Permission::firstOrCreate(['name' => 'permission.create']);
        Permission::firstOrCreate(['name' => 'permission.edit']);
        Permission::firstOrCreate(['name' => 'permission.delete']);

        // Permissions test
        Permission::firstOrCreate(['name' => 'test.index']);
        Permission::firstOrCreate(['name' => 'test.show']);
        Permission::firstOrCreate(['name' => 'test.create']);
        Permission::firstOrCreate(['name' => 'test.edit']);
        Permission::firstOrCreate(['name' => 'test.delete']);

        // Permissions ticket
        Permission::firstOrCreate(['name' => 'ticket.index']);
        Permission::firstOrCreate(['name' => 'ticket.show']);
        Permission::firstOrCreate(['name' => 'ticket.create']);
        Permission::firstOrCreate(['name' => 'ticket.edit']);
        Permission::firstOrCreate(['name' => 'ticket.delete']);

        // Permissions user
        Permission::firstOrCreate(['name' => 'user.index']);
        Permission::firstOrCreate(['name' => 'user.show']);
        Permission::firstOrCreate(['name' => 'user.create']);
        Permission::firstOrCreate(['name' => 'user.edit']);
        Permission::firstOrCreate(['name' => 'user.delete']);

        // Permissions génériques
        Permission::firstOrCreate(['name' => 'admin.access']);
        Permission::firstOrCreate(['name' => 'reports.access']);

        // === RÔLES ===
                // Super Admin : Tous les droits
        $superAdmin = Role::firstOrCreate(['name' => 'super-admin']);
        $superAdmin->syncPermissions(Permission::all());
        
        // Admin : Gestion complète
        $admin = Role::firstOrCreate(['name' => 'admin']);
        $admin->syncPermissions([
            'admin.access', 'reports.access', 'allowdomain.index', 'allowdomain.show', 'allowdomain.create', 'allowdomain.edit', 'allowdomain.delete', 'company.index', 'company.show', 'company.create', 'company.edit', 'company.delete', 'contact.index', 'contact.show', 'contact.create', 'contact.edit', 'contact.delete', 'locale.index', 'locale.show', 'locale.create', 'locale.edit', 'locale.delete', 'page.index', 'page.show', 'page.create', 'page.edit', 'page.delete', 'permission.index', 'permission.show', 'permission.create', 'permission.edit', 'permission.delete', 'test.index', 'test.show', 'test.create', 'test.edit', 'test.delete', 'ticket.index', 'ticket.show', 'ticket.create', 'ticket.edit', 'ticket.delete', 'user.index', 'user.show', 'user.create', 'user.edit', 'user.delete'
        ]);
        
        // Manager : Gestion limitée (pas de suppression)
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'allowdomain.index', 'allowdomain.show', 'allowdomain.create', 'allowdomain.edit', 'company.index', 'company.show', 'company.create', 'company.edit', 'contact.index', 'contact.show', 'contact.create', 'contact.edit', 'locale.index', 'locale.show', 'locale.create', 'locale.edit', 'page.index', 'page.show', 'page.create', 'page.edit', 'permission.index', 'permission.show', 'permission.create', 'permission.edit', 'test.index', 'test.show', 'test.create', 'test.edit', 'ticket.index', 'ticket.show', 'ticket.create', 'ticket.edit', 'user.index', 'user.show', 'user.edit'
        ]);
        
        // User : Accès aux tickets et lecture seule sur profil et company (PAS D'ACCÈS aux domaines)
        $user = Role::firstOrCreate(['name' => 'user']);
        $user->syncPermissions([
            'company.show', 'user.show', 'ticket.index', 'ticket.show', 'ticket.create', 'ticket.edit'
        ]);

        // === UTILISATEURS PAR DÉFAUT ===
        
        // Super Admin
        $superAdminUser = User::firstOrCreate([
            'email' => 'a@a.com'
        ], [
            'name' => 'Super Admin',
            'password' => bcrypt('azerty'),
            'status' => 'active',
            'agree_terms' => 'oui',
            'email_verified_at' => now()
        ]);
        $superAdminUser->assignRole('super-admin');

        $this->command->info('Permissions et rôles créés automatiquement !');
        $this->command->info('Super Admin : a@a.com / azerty');
    }
}