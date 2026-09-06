<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
   /**
    * Crée la table qui stocke les messages envoyés à l'administrateur.
    */
   public function up(): void
   {
       Schema::create('contact_messages', function (Blueprint $table) {
           $table->id();
           // Nom de la personne qui envoie le message.
           $table->string('name');
           // Adresse e-mail utilisée pour identifier l'utilisateur.
           $table->string('email');
           // Contenu du message envoyé à l'administrateur.
           $table->text('message');
           // Permet de savoir si l'admin a déjà lu le message.
           $table->boolean('lu')->default(false);
           $table->timestamps();
       });
   }
   /**
    * Supprime la table si la migration est annulée.
    */
   public function down(): void
   {
       Schema::dropIfExists('contact_messages');
   }
};