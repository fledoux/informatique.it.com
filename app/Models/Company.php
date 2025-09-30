<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['status','name','siret','vat_number','email','phone','website','address_line1','address_line2','zip','city','country','notes'];

    protected $casts = ['email_verified_at' => 'datetime','password' => 'hashed','created_at' => 'datetime','updated_at' => 'datetime','status' => 'string'];

    /**
     * Une société a plusieurs utilisateurs
     */
    public function users()
    {
        return $this->hasMany(\App\Models\User::class);
    }

    /**
     * Une société a plusieurs tickets
     */
    public function tickets()
    {
        return $this->hasMany(\App\Models\Ticket::class);
    }

    /**
     * Une société peut avoir plusieurs domaines autorisés pour l'inscription
     */
    public function allowedDomains()
    {
        return $this->hasMany(\App\Models\AllowDomainRegistration::class);
    }

    /**
     * Ajoute un domaine autorisé pour l'inscription automatique
     */
    public function addAllowedDomain(string $domain): \App\Models\AllowDomainRegistration
    {
        return $this->allowedDomains()->create(['domain' => $domain]);
    }

    /**
     * Vérifie si un domaine est autorisé pour cette société
     */
    public function isDomainAllowed(string $domain): bool
    {
        return $this->allowedDomains()->where('domain', $domain)->exists();
    }
}
