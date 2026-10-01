<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Revenu : représente une rentrée d'argent d'un utilisateur
 * (salaire, aide, etc.). Il est relié à la table "revenu".
 */
class Revenu extends Model
{
    // Nom de la table dans la base de données.
    protected $table = 'revenu';

    // Nom de la clé primaire.
    protected $primaryKey = 'idRevenu';

    // La table n'a pas de colonnes created_at et updated_at.
    public $timestamps = false;

    // Champs que l'on peut remplir avec Revenu::create([...]).
    protected $fillable = [
        'montant',
        'source',
        'dateRevenu',
        'idUtilisateur',
    ];
}
