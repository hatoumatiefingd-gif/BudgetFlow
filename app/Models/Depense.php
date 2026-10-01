<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle Depense : représente une dépense d'un utilisateur.
 * Il est relié à la table "depense" de la base de données.
 */
class Depense extends Model
{
    // Nom de la table dans la base de données.
    protected $table = 'depense';

    // Nom de la clé primaire (par défaut Laravel attend "id").
    protected $primaryKey = 'idDepense';

    // La table n'a pas de colonnes created_at et updated_at.
    public $timestamps = false;

    // Champs que l'on peut remplir avec Depense::create([...]).
    protected $fillable = [
        'montant',
        'description',
        'dateDepense',
        'idUtilisateur',
        'idCategorie',
    ];

    /**
     * Une dépense appartient à une catégorie.
     * Permet d'écrire $depense->categorie->nomCategorie.
     */
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'idCategorie', 'idCategorie');
    }
}
