<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ajoute le statut du compte utilisateur.
     * Un compte actif peut utiliser l'application.
     * Un compte désactivé ne doit plus pouvoir se connecter.
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {

            // Indique si le compte utilisateur est actif.
$table->boolean('actif')->default(true);
        });
    }

    /**
     * Supprime la colonne "actif" si la migration est annulée.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('actif');
        });
    }
};