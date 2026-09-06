<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepenseRecurrente extends Model

{

    protected $table = 'depenserecurrente';

    protected $primaryKey = 'idDepenseRecurrente';

    public $timestamps = false;

    protected $fillable = [

        'nomDepenseRecurrente',

        'montant',

        'frequence',

        'prochaineDate',

        'idCategorie',

        'idUtilisateur',

    ];

    public function categorie()

    {

        return $this->belongsTo(Categorie::class, 'idCategorie', 'idCategorie');

    }

}
 