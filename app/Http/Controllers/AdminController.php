<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

// Toutes les méthodes de ce contrôleur sont derrière le middleware role:admin
// (voir routes/web.php) : un utilisateur simple reçoit une erreur 403.
// L'admin voit les comptes et les messages, mais jamais les dépenses
// ni les revenus des utilisateurs (données personnelles).
class AdminController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TABLEAU DE BORD ADMINISTRATEUR
    |--------------------------------------------------------------------------
    */

    public function dashboard()
    {
        // Compte tous les comptes enregistrés : SELECT COUNT(*) FROM users
        $totalUtilisateurs = User::count();

        // Compte uniquement les comptes utilisateurs simples.
        $totalComptesUtilisateurs = User::where(
            'role',
            'utilisateur'
        )->count();

        // Compte les comptes administrateurs.
        $totalAdmins = User::where(
            'role',
            'admin'
        )->count();


        /*
        |--------------------------------------------------------------------------
        | DERNIERS UTILISATEURS
        |--------------------------------------------------------------------------
        */

        // Récupère les 5 derniers utilisateurs inscrits.
        $derniersUtilisateurs = User::where(
                'role',
                'utilisateur'
            )
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | ACTIVITÉ RÉCENTE ADMIN
        |--------------------------------------------------------------------------
        */

        /*
         * Transforme les dernières inscriptions
         * en activités visibles par l'administrateur.
         */
        $activitesUtilisateurs = User::where(
                'role',
                'utilisateur'
            )
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get()
            // map() transforme chaque utilisateur en un petit tableau
            // (type, titre, message, date) que la vue sait afficher.
            ->map(function ($user) {

                return [
                    'type' => 'inscription',

                    'titre' => 'Nouvel utilisateur',

                    'message' =>
                        $user->name .
                        ' vient de créer un compte.',

                    'date' => $user->created_at,
                ];
            });


        /*
         * Transforme les derniers messages de contact
         * en activités administratives.
         */
        $activitesMessages = ContactMessage::orderBy(
                'created_at',
                'desc'
            )
            ->take(5)
            ->get()
            ->map(function ($message) {

                // ?? : si name est vide, on essaie nom, sinon on met un texte par défaut.
                $nomExpediteur =
                    $message->name
                    ?? $message->nom
                    ?? 'Un utilisateur';

                return [
                    'type' => 'message',

                    'titre' => 'Nouveau message',

                    'message' =>
                        $nomExpediteur .
                        ' a envoyé un message de contact.',

                    'date' => $message->created_at,
                ];
            });


        /*
         * Regroupe les inscriptions et les messages,
         * puis garde seulement les 5 activités les plus récentes.
         */
        $activitesRecentes = $activitesUtilisateurs
            ->concat($activitesMessages) // met les deux listes bout à bout
            ->sortByDesc('date') // trie le tout par date, la plus récente d'abord
            ->take(5)
            ->values(); // renumérote la liste de 0 à 4 après le tri


        /*
        |--------------------------------------------------------------------------
        | AFFICHAGE DU TABLEAU DE BORD
        |--------------------------------------------------------------------------
        */

        return view(
            'admin.dashboard',
            compact(
                'totalUtilisateurs',
                'totalComptesUtilisateurs',
                'totalAdmins',
                'derniersUtilisateurs',
                'activitesRecentes'
            )
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MESSAGES DE CONTACT
    |--------------------------------------------------------------------------
    */

    public function messagesContact()
    {
        // Récupère les messages du plus récent au plus ancien.
        $messages = ContactMessage::orderBy(
            'created_at',
            'desc'
        )->get();

        return view(
            'admin.messages-contact',
            compact('messages')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LISTE DES UTILISATEURS
    |--------------------------------------------------------------------------
    */

    public function utilisateurs(Request $request)
    {
        // Récupère le texte saisi dans la barre de recherche.
        $recherche = $request->recherche;

        /*
         * when($recherche, ...) : la condition n'est ajoutée que si quelque chose
         * a été tapé dans la barre de recherche. Sinon, on affiche tout le monde.
         * like '%texte%' = "contient ce texte", n'importe où dans le nom ou l'e-mail.
         * En SQL : WHERE name LIKE '%texte%' OR email LIKE '%texte%'
         * Le texte tapé passe par une requête préparée : pas d'injection SQL.
         */
        $utilisateurs = User::when(
            $recherche,
            function ($query, $recherche) {

                return $query->where(function ($query) use ($recherche) {

                    $query
                        ->where(
                            'name',
                            'like',
                            '%' . $recherche . '%'
                        )
                        ->orWhere(
                            'email',
                            'like',
                            '%' . $recherche . '%'
                        );
                });
            }
        )
        ->orderBy('created_at', 'desc')
        ->get();


        // Compte tous les comptes enregistrés.
        $totalUtilisateurs = User::count();

        // Compte uniquement les comptes utilisateurs simples.
        $comptesActifs = User::where(
            'role',
            'utilisateur'
        )->count();

        // Compte les comptes administrateurs.
        $comptesAdmins = User::where(
            'role',
            'admin'
        )->count();


        return view(
            'admin.utilisateurs',
            compact(
                'utilisateurs',
                'recherche',
                'totalUtilisateurs',
                'comptesActifs',
                'comptesAdmins'
            )
        );
    }
}