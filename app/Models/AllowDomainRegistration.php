<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AllowDomainRegistration extends Model
{
    protected $fillable = ['company_id','domain'];

    protected $casts = ['email_verified_at' => 'datetime','password' => 'hashed','created_at' => 'datetime','updated_at' => 'datetime'];

    /**
     * Relation avec Company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Trouve une company autorisée pour un domaine email donné
     */
    public static function findCompanyByEmailDomain(string $email): ?Company
    {
        $domain = substr(strrchr($email, '@'), 1);
        
        $allowedDomain = self::where('domain', $domain)->first();
        
        return $allowedDomain?->company;
    }

    /**
     * Vérifie si un domaine est autorisé pour l'inscription automatique
     */
    public static function isDomainAllowed(string $email): bool
    {
        return self::findCompanyByEmailDomain($email) !== null;
    }
}
