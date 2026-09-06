<?php

namespace App\Http\Controllers;

use App\Models\DepenseRecurrente;
use App\Models\Categorie;
use App\Models\NotificationBudget;
use Illuminate\Http\Request;

class DepenseRecurrenteController extends Controller
{
    public function index()
    {
        /*
         * Lance le traitement automatique des dépenses récurrentes
         * avant d'afficher la liste.
         */
        app(DashboardController::class)->traiterDepensesRecurrentes();

        // Récupère les dépenses récurrentes de l'utilisateur connecté.
        $recurrentes = DepenseRecurrente::with('categorie')
            ->where('idUtilisateur', auth()->id())
            ->get();

        return view(
            'depenses-recurrentes.index',
            compact('recurrentes')
        );
    }

    public function create()
    {
        // Récupère les catégories disponibles.
        $categories = Categorie::all();

        return view(
            'depenses-recurrentes.create',
            compact('categories')
        );
    }

    public function store(Request $request)
    {
        /*
         * Vérifie les données saisies avant
         * d'enregistrer la dépense récurrente.
         */
        $request->validate([
            'nomDepenseRecurrente' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'frequence' => 'required',
            'prochaineDate' => 'required|date|after_or_equal:2026-07-01',
            'idCategorie' => 'required',
        ]);

        // Enregistre la nouvelle dépense récurrente.
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

        /*
         * Crée une notification pour informer
         * l'utilisateur du nouveau paiement à venir.
         */
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

        return redirect()
            ->route('depenses-recurrentes.index');
    }

    public function edit($id)
    {
        // Récupère uniquement la dépense appartenant à l'utilisateur.
        $recurrente = DepenseRecurrente::where(
                'idUtilisateur',
                auth()->id()
            )
            ->findOrFail($id);

        // Récupère les catégories pour le formulaire.
        $categories = Categorie::all();

        return view(
            'depenses-recurrentes.edit',
            compact('recurrente', 'categories')
        );
    }

    public function update(Request $request, $id)
    {
        /*
         * Vérifie les nouvelles données
         * avant la modification.
         */
        $request->validate([
            'nomDepenseRecurrente' => 'required|string|max:255',
            'montant' => 'required|numeric|min:0',
            'frequence' => 'required',
            'prochaineDate' => 'required|date|after_or_equal:2026-07-01',
            'idCategorie' => 'required',
        ]);

        // Recherche la dépense récurrente de l'utilisateur.
        $recurrente = DepenseRecurrente::where(
                'idUtilisateur',
                auth()->id()
            )
            ->findOrFail($id);

        // Enregistre les nouvelles informations.
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

    public function destroy($id)
    {
        // Recherche uniquement la dépense de l'utilisateur connecté.
        $recurrente = DepenseRecurrente::where(
                'idUtilisateur',
                auth()->id()
            )
            ->findOrFail($id);

        // Supprime la dépense récurrente.
        $recurrente->delete();

        return redirect()
            ->route('depenses-recurrentes.index');
    }
}