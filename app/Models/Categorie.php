<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Categorie : catégorie d'une dépense (Courses, Logement...).
 * Les catégories par défaut sont ajoutées par une migration.
 */
class Categorie extends Model
{
    // Nom de la table dans la base de données.
    protected $table = 'categorie';

    // Nom de la clé primaire.
    protected $primaryKey = 'idCategorie';

    // La table n'a pas de colonnes created_at et updated_at.
    public $timestamps = false;
}
