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
       if (!Schema::hasTable('categorie')) {
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
               $table->id('idDepense');
               $table->decimal('montant', 10, 2);
               $table->string('description', 255);
               $table->date('dateDepense');
               $table->unsignedBigInteger('idUtilisateur');
               $table->unsignedBigInteger('idCategorie');
               $table->foreign('idUtilisateur')
                   ->references('id')
                   ->on('users')
                   ->onDelete('cascade');
               $table->foreign('idCategorie')
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
               $table->string('frequence', 50);
               $table->date('prochaineDate');
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