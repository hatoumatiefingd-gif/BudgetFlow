<?php

namespace App\Http\Controllers;

use App\Models\DepenseRecurrente;
use App\Models\Categorie;
use App\Models\NotificationBudget;
use Illuminate\Http\Request;

class DepenseRecurrenteController extends Controller
{
    /**
     * Affiche les dépenses récurrentes
     * de l'utilisateur connecté.
     */
    public function index()
    {
        // Récupère les dépenses récurrentes de l'utilisateur connecté,
        // avec leur catégorie, de la plus proche échéance à la plus lointaine.
        $recurrentes = DepenseRecurrente::with('categorie')
            ->where('idUtilisateur', auth()->id())
            ->orderBy('prochaineDate', 'asc') // asc = croissant : le prochain paiement en haut
            ->get();

        return view(
            'depenses-recurrentes.index',
            compact('recurrentes')
        );
    }


    /**
     * Affiche le formulaire d'ajout.
     */
    public function create()
    {
        // Récupère toutes les catégories pour remplir la liste déroulante.
        $categories = Categorie::all();

        return view(
            'depenses-recurrentes.create',
            compact('categories')
        );
    }


    /**
     * Enregistre une nouvelle dépense récurrente.
     */
    public function store(Request $request)
    {
        // Vérifie les données du formulaire.
        // "in:" oblige à choisir une des trois fréquences proposées.
        // "after_or_equal:today" interdit une date déjà passée.
        $request->validate([
            'nomDepenseRecurrente' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'frequence' => 'required|in:Mensuel,Hebdomadaire,Annuel',
            'prochaineDate' => 'required|date|after_or_equal:today',
            'idCategorie' => 'required',
        ], [
            'prochaineDate.after_or_equal' =>
                'La prochaine date doit être aujourd’hui ou une date future.',
        ]);

        // On enregistre seulement l'abonnement. La vraie dépense n'est pas créée
        // ici : c'est DashboardController::traiterDepensesRecurrentes() qui l'ajoute
        // quand la prochaineDate arrive.
        $recurrente = DepenseRecurrente::create([
            'nomDepenseRecurrente' =>
                $request->nomDepenseRecurrente,

            'montant' =>
                $request->montant,

            'frequence' =>
                $request->frequence,

            'prochaineDate' =>
                $request->prochaineDate,

            'idCategorie' =>
                $request->idCategorie,

            'idUtilisateur' =>
                auth()->id(),
        ]);

        // Crée une notification pour prévenir l'utilisateur du futur paiement.
        NotificationBudget::create([
            'titre' => 'Paiement à venir',

            'message' =>
                'Votre dépense récurrente "' .
                $recurrente->nomDepenseRecurrente .
                '" a été ajoutée. Montant : ' .
                $recurrente->montant .
                ' €.',

            'type' => 'Paiement',

            'dateNotification' =>
                now()->toDateString(),

            'idUtilisateur' =>
                auth()->id(),
        ]);

        // Retourne à la liste des dépenses récurrentes.
        return redirect()
            ->route('depenses-recurrentes.index');
    }


    /**
     * Affiche le formulaire de modification.
     */
    public function edit($id)
    {
        // Recherche la dépense récurrente uniquement parmi celles de l'utilisateur :
        // impossible de modifier celle d'un autre compte (erreur 404 sinon).
        $recurrente = DepenseRecurrente::where(
                'idUtilisateur',
                auth()->id()
            )
            ->findOrFail($id);

        $categories = Categorie::all();

        return view(
            'depenses-recurrentes.edit',
            compact('recurrente', 'categories')
        );
    }


    /**
     * Modifie une dépense récurrente existante.
     */
    public function update(Request $request, $id)
    {
        // Vérifie les nouvelles données (mêmes règles que pour l'ajout).
        $request->validate([
            'nomDepenseRecurrente' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'frequence' => 'required|in:Mensuel,Hebdomadaire,Annuel',
            'prochaineDate' => 'required|date|after_or_equal:today',
            'idCategorie' => 'required',
        ], [
            'prochaineDate.after_or_equal' =>
                'La prochaine date doit être aujourd’hui ou une date future.',
        ]);

        // Recherche la dépense uniquement parmi celles de l'utilisateur connecté.
        $recurrente = DepenseRecurrente::where(
                'idUtilisateur',
                auth()->id()
            )
            ->findOrFail($id);

        // Enregistre les modifications.
        $recurrente->update([
            'nomDepenseRecurrente' =>
                $request->nomDepenseRecurrente,

            'montant' =>
                $request->montant,

            'frequence' =>
                $request->frequence,

            'prochaineDate' =>
                $request->prochaineDate,

            'idCategorie' =>
                $request->idCategorie,
        ]);

        return redirect()
            ->route('depenses-recurrentes.index');
    }


    /**
     * Supprime une dépense récurrente.
     */
    public function destroy($id)
    {
        // Recherche la dépense uniquement parmi celles de l'utilisateur connecté.
        $recurrente = DepenseRecurrente::where(
                'idUtilisateur',
                auth()->id()
            )
            ->findOrFail($id);

        // Supprime seulement l'abonnement : les dépenses déjà ajoutées les mois
        // précédents restent dans l'historique (ce sont des lignes de la table depense).
        $recurrente->delete();

        return redirect()
            ->route('depenses-recurrentes.index');
    }
}