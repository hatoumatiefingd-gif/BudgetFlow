<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
return new class extends Migration
{
   /**
    * Ajoute les catégories de base de BudgetFlow.
    */
   public function up(): void
   {
       // Ajoute les catégories seulement si la table est vide.
       if (DB::table('categorie')->count() === 0) {
           DB::table('categorie')->insert([
               ['nomCategorie' => 'Courses'],
               ['nomCategorie' => 'Logement'],
               ['nomCategorie' => 'Transport'],
               ['nomCategorie' => 'Loisirs'],
               ['nomCategorie' => 'Santé'],
               ['nomCategorie' => 'Études'],
               ['nomCategorie' => 'Autres'],
           ]);
       }
   }
   /**
    * Supprime les catégories ajoutées par cette migration.
    */
   public function down(): void
   {
       DB::table('categorie')
           ->whereIn('nomCategorie', [
               'Courses',
               'Logement',
               'Transport',
               'Loisirs',
               'Santé',
               'Études',
               'Autres',
           ])
           ->delete();
   }
};