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
                // Permissions allowdomainregistration
        Permission::firstOrCreate(['name' => 'allowdomainregistration.index']);
        Permission::firstOrCreate(['name' => 'allowdomainregistration.show']);
        Permission::firstOrCreate(['name' => 'allowdomainregistration.create']);
        Permission::firstOrCreate(['name' => 'allowdomainregistration.edit']);
        Permission::firstOrCreate(['name' => 'allowdomainregistration.delete']);

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

        // Permissions testentity
        Permission::firstOrCreate(['name' => 'testentity.index']);
        Permission::firstOrCreate(['name' => 'testentity.show']);
        Permission::firstOrCreate(['name' => 'testentity.create']);
        Permission::firstOrCreate(['name' => 'testentity.edit']);
        Permission::firstOrCreate(['name' => 'testentity.delete']);

        // Permissions ticketattachment
        Permission::firstOrCreate(['name' => 'ticketattachment.index']);
        Permission::firstOrCreate(['name' => 'ticketattachment.show']);
        Permission::firstOrCreate(['name' => 'ticketattachment.create']);
        Permission::firstOrCreate(['name' => 'ticketattachment.edit']);
        Permission::firstOrCreate(['name' => 'ticketattachment.delete']);

        // Permissions ticket
        Permission::firstOrCreate(['name' => 'ticket.index']);
        Permission::firstOrCreate(['name' => 'ticket.show']);
        Permission::firstOrCreate(['name' => 'ticket.create']);
        Permission::firstOrCreate(['name' => 'ticket.edit']);
        Permission::firstOrCreate(['name' => 'ticket.delete']);

        // Permissions ticketmessage
        Permission::firstOrCreate(['name' => 'ticketmessage.index']);
        Permission::firstOrCreate(['name' => 'ticketmessage.show']);
        Permission::firstOrCreate(['name' => 'ticketmessage.create']);
        Permission::firstOrCreate(['name' => 'ticketmessage.edit']);
        Permission::firstOrCreate(['name' => 'ticketmessage.delete']);

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
            'admin.access', 'reports.access', 'allowdomainregistration.index', 'allowdomainregistration.show', 'allowdomainregistration.create', 'allowdomainregistration.edit', 'allowdomainregistration.delete', 'company.index', 'company.show', 'company.create', 'company.edit', 'company.delete', 'contact.index', 'contact.show', 'contact.create', 'contact.edit', 'contact.delete', 'locale.index', 'locale.show', 'locale.create', 'locale.edit', 'locale.delete', 'page.index', 'page.show', 'page.create', 'page.edit', 'page.delete', 'permission.index', 'permission.show', 'permission.create', 'permission.edit', 'permission.delete', 'test.index', 'test.show', 'test.create', 'test.edit', 'test.delete', 'testentity.index', 'testentity.show', 'testentity.create', 'testentity.edit', 'testentity.delete', 'ticketattachment.index', 'ticketattachment.show', 'ticketattachment.create', 'ticketattachment.edit', 'ticketattachment.delete', 'ticket.index', 'ticket.show', 'ticket.create', 'ticket.edit', 'ticket.delete', 'ticketmessage.index', 'ticketmessage.show', 'ticketmessage.create', 'ticketmessage.edit', 'ticketmessage.delete', 'user.index', 'user.show', 'user.create', 'user.edit', 'user.delete'
        ]);
        
        // Manager : Gestion limitée (pas de suppression)
        $manager = Role::firstOrCreate(['name' => 'manager']);
        $manager->syncPermissions([
            'allowdomainregistration.index', 'allowdomainregistration.show', 'allowdomainregistration.create', 'allowdomainregistration.edit', 'company.index', 'company.show', 'company.create', 'company.edit', 'contact.index', 'contact.show', 'contact.create', 'contact.edit', 'locale.index', 'locale.show', 'locale.create', 'locale.edit', 'page.index', 'page.show', 'page.create', 'page.edit', 'permission.index', 'permission.show', 'permission.create', 'permission.edit', 'test.index', 'test.show', 'test.create', 'test.edit', 'testentity.index', 'testentity.show', 'testentity.create', 'testentity.edit', 'ticketattachment.index', 'ticketattachment.show', 'ticketattachment.create', 'ticketattachment.edit', 'ticket.index', 'ticket.show', 'ticket.create', 'ticket.edit', 'ticketmessage.index', 'ticketmessage.show', 'ticketmessage.create', 'ticketmessage.edit', 'user.index', 'user.show', 'user.edit'
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