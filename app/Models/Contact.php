<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Contact extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = ['name','email','phone','type','need'];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = ['email_verified_at' => 'datetime','password' => 'hashed','created_at' => 'datetime','updated_at' => 'datetime'];

    /**
     * Types de demande possibles
     */
    public static function getTypes(): array
    {
        return [
            'devis' => 'Demande de devis',
            'info' => 'Informations générales', 
            'urgence' => 'Urgence technique',
            'partenariat' => 'Partenariat',
            'autre' => 'Autre',
        ];
    }

    /**
     * Obtenir le libellé du type
     */
    public function getTypeLabel(): string
    {
        return self::getTypes()[$this->type] ?? $this->type;
    }
}
