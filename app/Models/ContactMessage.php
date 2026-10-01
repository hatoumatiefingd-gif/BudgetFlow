<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle ContactMessage : message envoyé depuis le formulaire de contact.
 * L'administrateur peut lire ces messages dans son espace.
 * La table utilise les colonnes created_at et updated_at de Laravel.
 */
class ContactMessage extends Model
{
    // Champs autorisés lors de la création d'un message.
    protected $fillable = [
        'name',
        'email',
        'message',
        'lu',  // true si l'administrateur a lu le message
    ];
}
