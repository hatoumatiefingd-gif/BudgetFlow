<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le rôle de l'utilisateur.
     * Le rôle "utilisateur" donne accès à l'espace personnel.
     * Le rôle "admin" donne accès à l'espace administrateur.
     */
    public function up(): void
    {
        // On ajoute la colonne seulement si elle n'existe pas encore,
        // pour ne pas casser une base où elle a déjà été créée.
        if (! Schema::hasColumn('users', 'role')) {

            Schema::table('users', function (Blueprint $table) {

                // Par défaut, un nouveau compte est un compte utilisateur.
                $table->string('role', 20)->default('utilisateur');
            });
        }
    }

    /**
     * Supprime la colonne "role" si la migration est annulée.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('role');
        });
    }
};
