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
        $recurrentes = DepenseRecurrente::with('categorie')
            ->where('idUtilisateur', auth()->id())
            ->orderBy('prochaineDate', 'asc')
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


    /**
     * Affiche le formulaire de modification.
     */
    public function edit($id)
    {
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

        $recurrente = DepenseRecurrente::where(
                'idUtilisateur',
                auth()->id()
            )
            ->findOrFail($id);

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
        $recurrente = DepenseRecurrente::where(
                'idUtilisateur',
                auth()->id()
            )
            ->findOrFail($id);

        $recurrente->delete();

        return redirect()
            ->route('depenses-recurrentes.index');
    }
}