<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle NotificationBudget : une alerte affichée à l'utilisateur
 * (budget dépassé, bonne épargne, paiement à venir...).
 * Il est relié à la table "notification".
 */
class NotificationBudget extends Model
{
    // Nom de la table dans la base de données.
    protected $table = 'notification';

    // Nom de la clé primaire.
    protected $primaryKey = 'idNotification';

    // La table n'a pas de colonnes created_at et updated_at.
    public $timestamps = false;

    // Champs que l'on peut remplir avec NotificationBudget::create([...]).
    protected $fillable = [
        'titre',
        'message',
        'type',              // Alerte, Paiement, Réussite ou Information
        'dateNotification',
        'idUtilisateur',
    ];
}
