<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle NotificationAdmin : notification destinée à l'administrateur
 * (par exemple quand un nouvel utilisateur crée un compte).
 */
class NotificationAdmin extends Model
{
    // Nom de la table dans la base de données.
    protected $table = 'notification_admin';

    // Nom de la clé primaire.
    protected $primaryKey = 'idNotification';

    // La table n'a pas de colonnes created_at et updated_at.
    public $timestamps = false;

    // Champs que l'on peut remplir avec NotificationAdmin::create([...]).
    protected $fillable = [
        'titre',
        'message',
        'type',
        'dateNotification',
    ];
}
