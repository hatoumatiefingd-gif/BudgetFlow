<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
   /**
    * Crée les tables principales de BudgetFlow
    * lorsqu'elles n'existent pas encore.
    */
   public function up(): void
   {
       // Crée la table des catégories.
       if (!Schema::hasTable('categorie')) { // seulement si la table n'existe pas encore (pas d'erreur si on relance)
           Schema::create('categorie', function (Blueprint $table) {
               $table->id('idCategorie');
               $table->string('nomCategorie', 50);
           });
       }
       // Crée la table des revenus.
       if (!Schema::hasTable('revenu')) {
           Schema::create('revenu', function (Blueprint $table) {
               $table->id('idRevenu');
               $table->string('source', 50);
               $table->decimal('montant', 10, 2);
               $table->date('dateRevenu');
               $table->unsignedBigInteger('idUtilisateur');
               $table->foreign('idUtilisateur')
                   ->references('id')
                   ->on('users')
                   ->onDelete('cascade');
           });
       }
       // Crée la table des dépenses.
       if (!Schema::hasTable('depense')) {
           Schema::create('depense', function (Blueprint $table) {
               $table->id('idDepense'); // clé primaire : numéro unique, auto-incrémenté
               $table->decimal('montant', 10, 2); // 10 chiffres dont 2 après la virgule (centimes), sans erreur d'arrondi
               $table->string('description', 255); // VARCHAR(255)
               $table->date('dateDepense'); // date seule, sans l'heure
               $table->unsignedBigInteger('idUtilisateur'); // même type que users.id, obligatoire pour la clé étrangère
               $table->unsignedBigInteger('idCategorie');
               $table->foreign('idUtilisateur') // clé étrangère : la dépense appartient à un utilisateur
                   ->references('id')
                   ->on('users') // qui doit exister dans la table users
                   ->onDelete('cascade'); // compte supprimé = ses dépenses supprimées aussi
               $table->foreign('idCategorie') // clé étrangère vers la table categorie
                   ->references('idCategorie')
                   ->on('categorie')
                   ->onDelete('cascade');
           });
       }
       // Crée la table des dépenses récurrentes.
       if (!Schema::hasTable('depenserecurrente')) {
           Schema::create('depenserecurrente', function (Blueprint $table) {
               $table->id('idDepenseRecurrente');
               $table->string('nomDepenseRecurrente', 255);
               $table->decimal('montant', 10, 2);
               $table->string('frequence', 50); // Mensuel, Hebdomadaire ou Annuel
               $table->date('prochaineDate'); // date du prochain paiement, avancée automatiquement
               $table->unsignedBigInteger('idCategorie');
               $table->unsignedBigInteger('idUtilisateur');
               $table->foreign('idCategorie')
                   ->references('idCategorie')
                   ->on('categorie')
                   ->onDelete('cascade');
               $table->foreign('idUtilisateur')
                   ->references('id')
                   ->on('users')
                   ->onDelete('cascade');
           });
       }
       // Crée la table des notifications utilisateur.
       if (!Schema::hasTable('notification')) {
           Schema::create('notification', function (Blueprint $table) {
               $table->id('idNotification');
               $table->string('titre', 255);
               $table->text('message');
               $table->string('type', 50);
               $table->date('dateNotification');
               $table->unsignedBigInteger('idUtilisateur');
               $table->foreign('idUtilisateur')
                   ->references('id')
                   ->on('users')
                   ->onDelete('cascade');
           });
       }
   }
   /**
    * Supprime les tables dans l'ordre inverse.
    * Lancée par php artisan migrate:rollback. On supprime d'abord les tables qui
    * ont des clés étrangères, sinon MySQL refuse (une dépense pointe vers une catégorie).
    */
   public function down(): void
   {
       Schema::dropIfExists('notification');
       Schema::dropIfExists('depenserecurrente');
       Schema::dropIfExists('depense');
       Schema::dropIfExists('revenu');
       Schema::dropIfExists('categorie');
   }
};