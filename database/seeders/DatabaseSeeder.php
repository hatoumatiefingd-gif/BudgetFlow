<?php

namespace Database\Seeders;

use App\Models\Categorie;
use App\Models\ContactMessage;
use App\Models\Depense;
use App\Models\DepenseRecurrente;
use App\Models\NotificationBudget;
use App\Models\Revenu;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Remplit la base avec des données de démonstration.
     * Commande : php artisan db:seed
     */
    public function run(): void
    {
        /*
        |--------------------------------------------------------------------------
        | COMPTES DE DÉMONSTRATION
        |--------------------------------------------------------------------------
        */

        // Compte administrateur.
        // L'application ne doit avoir qu'un seul administrateur :
        // on le crée seulement si la base n'en contient pas encore.
        if (! User::where('role', 'admin')->exists()) {
            User::create([
                'name' => 'Admin BudgetFlow',
                'email' => 'admin@budgetflow.fr',
                'password' => Hash::make('Admin123!'),
                'role' => 'admin',
            ]);
        }

        // Si les données de démo ont déjà été créées, on s'arrête là
        // pour ne pas les ajouter une deuxième fois.
        if (User::where('email', 'demo@budgetflow.fr')->exists()) {
            return;
        }

        // Compte utilisateur qui contiendra les dépenses et revenus de démo.
        $user = User::create([
            'name' => 'Marie Dupont',
            'email' => 'demo@budgetflow.fr',
            'password' => Hash::make('Demo123!'),
            'role' => 'utilisateur',
        ]);


        /*
        |--------------------------------------------------------------------------
        | REVENUS DU MOIS
        |--------------------------------------------------------------------------
        */

        Revenu::create([
            'source' => 'Salaire alternance',
            'montant' => 1450,
            'dateRevenu' => now()->startOfMonth()->toDateString(),
            'idUtilisateur' => $user->id,
        ]);

        Revenu::create([
            'source' => 'Aide au logement',
            'montant' => 220,
            'dateRevenu' => now()->startOfMonth()->addDays(4)->toDateString(),
            'idUtilisateur' => $user->id,
        ]);


        /*
        |--------------------------------------------------------------------------
        | DÉPENSES DU MOIS
        |--------------------------------------------------------------------------
        */

        // Récupère les catégories par leur nom (ex : $categories['Courses']).
        $categories = Categorie::pluck('idCategorie', 'nomCategorie');

        // Liste des dépenses : [description, montant, catégorie].
        $depenses = [
            ['Loyer', 520, 'Logement'],
            ['Supermarché', 86.40, 'Courses'],
            ['Pass Navigo', 86.40, 'Transport'],
            ['Cinéma', 12.50, 'Loisirs'],
            ['Pharmacie', 18.90, 'Santé'],
            ['Livres de cours', 34, 'Études'],
        ];

        foreach ($depenses as $numero => $depense) {
            Depense::create([
                'description' => $depense[0],
                'montant' => $depense[1],
                'dateDepense' => now()->startOfMonth()->addDays($numero)->toDateString(),
                'idCategorie' => $categories[$depense[2]],
                'idUtilisateur' => $user->id,
            ]);
        }


        /*
        |--------------------------------------------------------------------------
        | DÉPENSE RÉCURRENTE
        |--------------------------------------------------------------------------
        */

        DepenseRecurrente::create([
            'nomDepenseRecurrente' => 'Abonnement téléphone',
            'montant' => 15.99,
            'frequence' => 'Mensuel',
            'prochaineDate' => now()->addMonth()->startOfMonth()->toDateString(),
            'idCategorie' => $categories['Autres'],
            'idUtilisateur' => $user->id,
        ]);


        /*
        |--------------------------------------------------------------------------
        | NOTIFICATION ET MESSAGE DE CONTACT
        |--------------------------------------------------------------------------
        */

        NotificationBudget::create([
            'titre' => 'Bienvenue',
            'message' => 'Votre espace BudgetFlow est prêt.',
            'type' => 'Information',
            'dateNotification' => now()->startOfMonth()->toDateString(),
            'idUtilisateur' => $user->id,
        ]);

        ContactMessage::create([
            'name' => 'Lucas Martin',
            'email' => 'lucas@example.com',
            'message' => 'Bonjour, comment exporter mes dépenses ?',
        ]);
    }
}
