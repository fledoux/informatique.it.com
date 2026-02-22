<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = ['name','email','password','status','company_id','firstname','lastname','initial','phone','last_login','agree_terms','channels','note'];

    protected $casts = ['email_verified_at' => 'datetime','password' => 'hashed','created_at' => 'datetime','updated_at' => 'datetime','status' => 'string','agree_terms' => 'string','channels' => 'array'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    


    public function company()
    {
        return $this->belongsTo(\App\Models\Company::class);
    }

    /**
     * Obtenir l'ID de la société de l'utilisateur
     */
    public function getCompanyId(): ?int
    {
        return $this->company_id;
    }

    /**
     * Vérifier si l'utilisateur appartient à une société
     */
    public function hasCompany(): bool
    {
        return !is_null($this->company_id);
    }

    /**
     * Obtenir le nom de la société
     */
    public function getCompanyName(): ?string
    {
        return $this->company?->name;
    }

    /**
     * Obtenir tous les rôles disponibles
     */
    public static function getAvailableRoles(): array
    {
        return \Spatie\Permission\Models\Role::orderBy('name')->pluck('name', 'name')->toArray();
    }

    /**
     * Tickets créés par cet utilisateur (en tant qu'auteur)
     */
    public function authoredTickets()
    {
        return $this->hasMany(\App\Models\Ticket::class, 'author_id');
    }

    /**
     * Tickets assignés à cet utilisateur
     */
    public function assignedTickets()
    {
        return $this->hasMany(\App\Models\Ticket::class, 'assigned_to');
    }

    /**
     * Messages de tickets créés par cet utilisateur
     */
    public function ticketMessages()
    {
        return $this->hasMany(\App\Models\TicketMessage::class, 'author_id');
    }

    /**
     * Send the email verification notification with globalMail.
     *
     * @return void
     */
    public function sendEmailVerificationNotification()
    {
        // Générer l'URL de vérification
        $verificationUrl = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'verification.verify',
            \Carbon\Carbon::now()->addMinutes(60), // Expire dans 60 minutes
            [
                'id' => $this->id,
                'hash' => sha1($this->getEmailForVerification()),
            ]
        );

        // Envoyer l'email directement avec globalMail (pas de queue)
        // \Illuminate\Support\Facades\Mail::to($this->email)->send(new \App\Mail\VerifyEmailMail($this, $verificationUrl));
    }
}
