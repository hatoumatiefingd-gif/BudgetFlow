<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Modèle User : un compte de l'application.
 * Le champ "role" vaut "utilisateur" ou "admin".
 */
// User hérite de Authenticatable (et pas de Model directement) : c'est la classe
// de Laravel qui sait gérer la connexion, la session et le mot de passe.
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Champs que l'on peut remplir avec User::create([...]).
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role', // utilisé par le seeder pour créer l'admin ; l'inscription ne le remplit jamais
    ];

    /**
     * Champs cachés : ils ne sont jamais renvoyés (par exemple en JSON).
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Conversions automatiques : la date de vérification devient un objet date
     * et le mot de passe est haché automatiquement à l'enregistrement.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }
}
