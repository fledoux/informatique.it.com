<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    protected $fillable = ['status','name','siret','vat_number','email','phone','website','address_line1','address_line2','zip','city','country','notes'];

    protected $casts = ['email_verified_at' => 'datetime','password' => 'hashed','created_at' => 'datetime','updated_at' => 'datetime'];

    //
}
