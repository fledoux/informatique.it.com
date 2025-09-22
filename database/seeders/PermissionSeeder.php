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

        // Permissions page
        Permission::firstOrCreate(['name' => 'page.index']);
        Permission::firstOrCreate(['name' => 'page.show']);
        Permission::firstOrCreate(['name' => 'page.create']);
        Permission::firstOrCreate(['name' => 'page.edit']);
        Permission::firstOrCreate(['name' => 'page.delete']);

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
            'admin.access', 'reports.access', 'company.index', 'company.show', 'company.create', 'company.edit', 'company.delete', 'contact.index', 'contact.show', 'contact.create', 'contact.edit', 'contact.delete', 'page.index', 'page.show', 'page.create', 'page.edit', 'page.delete', 'ticket.index', 'ticket.show', 'ticket.create', 'ticket.edit', 'ticket.delete', 'user.index', 'user.show', 'user.create', 'user.edit', 'user.delete'
        ]);
        
        // Manager : Gestion limitée (pas de suppression)
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'company.index', 'company.show', 'company.create', 'company.edit', 'contact.index', 'contact.show', 'contact.create', 'contact.edit', 'page.index', 'page.show', 'page.create', 'page.edit', 'ticket.index', 'ticket.show', 'ticket.create', 'ticket.edit', 'user.index', 'user.show', 'user.edit'
        ]);
        
        // User : Lecture seule sur profil et company
        $user = Role::firstOrCreate(['name' => 'user']);
        $user->syncPermissions([
            'company.show', 'user.show'
        ]);

        // === UTILISATEUR SUPER ADMIN PAR DÉFAUT ===
        $superAdminUser = User::firstOrCreate([
            'email' => 'a@a.com'
        ], [
            'name' => 'Super Admin',           // AJOUTER
            'password' => bcrypt('azerty'),  // AJOUTER
            'status' => 'active',
            'agree_terms' => 'oui'
        ]);
        $superAdminUser->assignRole('super-admin');

        $this->command->info('Permissions et rôles créés automatiquement !');
        $this->command->info('Super Admin : a@a.com / azerty');
    }
}