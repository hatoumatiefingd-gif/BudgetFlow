<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Modèle DepenseRecurrente : une dépense qui revient régulièrement
 * (abonnement, loyer...). Quand la prochaine date est atteinte,
 * une vraie dépense est créée automatiquement (voir DashboardController).
 */
class DepenseRecurrente extends Model
{
    // Nom de la table dans la base de données.
    protected $table = 'depenserecurrente';

    // Nom de la clé primaire.
    protected $primaryKey = 'idDepenseRecurrente';

    // La table n'a pas de colonnes created_at et updated_at.
    public $timestamps = false;

    // Champs que l'on peut remplir avec DepenseRecurrente::create([...]).
    protected $fillable = [
        'nomDepenseRecurrente',
        'montant',
        'frequence',      // Mensuel, Hebdomadaire ou Annuel
        'prochaineDate',  // Date de la prochaine échéance
        'idCategorie',
        'idUtilisateur',
    ];

    /**
     * Une dépense récurrente appartient à une catégorie.
     */
    public function categorie()
    {
        return $this->belongsTo(Categorie::class, 'idCategorie', 'idCategorie');
    }
}
