<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WifiPassword extends Model
{
    protected $fillable = [
        'network_name',
        'password',
        'changed_at',
    ];

    protected $casts = [
        'password' => 'encrypted',
        'changed_at' => 'datetime',
    ];

    // Obtenir le dernier mot de passe
    public static function getLatest()
    {
        return static::latest('changed_at')->first();
    }
}
