<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Revenu extends Model
{
    protected $table = 'revenu';
    protected $primaryKey = 'idRevenu';
    public $timestamps = false;

    protected $fillable = [
        'montant',
        'source',
        'dateRevenu',
        'idUtilisateur'
    ];
}