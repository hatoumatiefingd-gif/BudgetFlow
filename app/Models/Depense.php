<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Depense extends Model
{

   protected $table = 'depense';
   protected $primaryKey = 'idDepense';
   public $timestamps = false;
   protected $fillable = [
    'montant',
    'description',
    'dateDepense',
    'idUtilisateur',
    'idCategorie'
];
   public function categorie()
   {

       return $this->belongsTo(Categorie::class, 'idCategorie', 'idCategorie');
   }
}