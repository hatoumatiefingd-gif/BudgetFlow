<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\Categorie;
use App\Models\Revenu;
use App\Models\NotificationBudget;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DepenseController extends Controller
{
    /**
     * Affiche les dépenses d'un mois précis.
     */
    public function index(Request $request)
    {
        // Utilise le mois actuel si aucun mois n'est sélectionné.
        $mois = $request->mois ?: now()->month;

        // Utilise l'année actuelle si aucune année n'est sélectionnée.
        $annee = $request->annee ?: now()->year;

        $query = Depense::with('categorie')
            ->where('idUtilisateur', auth()->id())

            // Affiche uniquement les dépenses du mois choisi.
            ->whereMonth('dateDepense', $mois)
            ->whereYear('dateDepense', $annee);

        // Filtre facultatif par catégorie.
        if ($request->categorie) {
            $query->where('idCategorie', $request->categorie);
        }

        $depenses = $query
            ->orderBy('dateDepense', 'desc')
            ->get();

        $categories = Categorie::all();

        return view('depenses.index', compact(
            'depenses',
            'categories',
            'mois',
            'annee'
        ));
    }


    /**
     * Affiche le formulaire d'ajout.
     */
    public function create()
    {
        $categories = Categorie::all();

        return view('depenses.create', compact('categories'));
    }


    /**
     * Enregistre une nouvelle dépense.
     */
    public function store(Request $request)
    {
        // Premier et dernier jour du mois actuel.
        $debutMois = now()->startOfMonth()->toDateString();
        $finMois = now()->endOfMonth()->toDateString();

        // Empêche l'utilisateur d'enregistrer une dépense
        // dans un ancien mois ou dans un mois futur.
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'dateDepense' => [
                'required',
                'date',
                'after_or_equal:' . $debutMois,
                'before_or_equal:' . $finMois,
            ],
            'idCategorie' => 'required',
        ], [
            'dateDepense.after_or_equal' =>
                'Vous ne pouvez pas ajouter une dépense dans un mois déjà passé.',

            'dateDepense.before_or_equal' =>
                'La dépense doit appartenir au mois actuel.',
        ]);


        // Création réelle de la dépense.
        Depense::create([
            'montant' => $request->montant,
            'description' => $request->description,
            'dateDepense' => $request->dateDepense,
            'idUtilisateur' => auth()->id(),
            'idCategorie' => $request->idCategorie,
        ]);


        // Notification après l'ajout.
        NotificationBudget::create([
            'titre' => 'Dépense ajoutée',
            'message' =>
                'Votre dépense "' .
                $request->description .
                '" a été enregistrée avec succès.',
            'type' => 'Réussite',
            'dateNotification' => now()->toDateString(),
            'idUtilisateur' => auth()->id(),
        ]);


        // Vérifie ensuite l'état réel du budget.
        $this->verifierBudget();


        return redirect()->route('depenses.index');
    }


    /**
     * Affiche le formulaire de modification.
     */
    public function edit($id)
    {
        $depense = Depense::where(
            'idUtilisateur',
            auth()->id()
        )->findOrFail($id);

        $categories = Categorie::all();

        return view(
            'depenses.edit',
            compact('depense', 'categories')
        );
    }


    /**
     * Modifie une dépense.
     */
    public function update(Request $request, $id)
    {
        $depense = Depense::where(
            'idUtilisateur',
            auth()->id()
        )->findOrFail($id);


        $request->validate([
            'montant' => 'required|numeric|min:0',
            'description' => 'required|string|max:255',
            'dateDepense' => 'required|date',
            'idCategorie' => 'required',
        ]);


        $ancienneDate = Carbon::parse($depense->dateDepense);
        $nouvelleDate = Carbon::parse($request->dateDepense);


        /*
         * Si la dépense appartient à un ancien mois,
         * sa date historique ne peut plus être changée.
         */
        if (
            $ancienneDate->lt(now()->startOfMonth()) &&
            !$nouvelleDate->isSameDay($ancienneDate)
        ) {
            return back()
                ->withErrors([
                    'dateDepense' =>
                        'La date d’une dépense d’un ancien mois ne peut plus être modifiée.'
                ])
                ->withInput();
        }


        /*
         * Pour une dépense du mois actuel,
         * la nouvelle date doit rester dans le mois actuel.
         */
        if (
            $ancienneDate->isSameMonth(now()) &&
            (
                $nouvelleDate->lt(now()->startOfMonth()) ||
                $nouvelleDate->gt(now()->endOfMonth())
            )
        ) {
            return back()
                ->withErrors([
                    'dateDepense' =>
                        'La date doit appartenir au mois actuel.'
                ])
                ->withInput();
        }


        // Mise à jour de la dépense.
        $depense->update([
            'montant' => $request->montant,
            'description' => $request->description,
            'dateDepense' => $request->dateDepense,
            'idCategorie' => $request->idCategorie,
        ]);


        // Recalcule les alertes du budget.
        $this->verifierBudget();


        return redirect()->route('depenses.index');
    }


    /**
     * Supprime une dépense.
     */
    public function destroy($id)
    {
        $depense = Depense::where(
            'idUtilisateur',
            auth()->id()
        )->findOrFail($id);

        $depense->delete();

        return redirect()->route('depenses.index');
    }


    /**
     * Vérifie automatiquement l'état du budget mensuel.
     */
    private function verifierBudget()
    {
        $mois = now()->month;
        $annee = now()->year;
        $userId = auth()->id();


        // Total réel des dépenses du mois.
        $totalDepenses = Depense::where(
            'idUtilisateur',
            $userId
        )
            ->whereMonth('dateDepense', $mois)
            ->whereYear('dateDepense', $annee)
            ->sum('montant');


        // Total réel des revenus du mois.
        $totalRevenus = Revenu::where(
            'idUtilisateur',
            $userId
        )
            ->whereMonth('dateRevenu', $mois)
            ->whereYear('dateRevenu', $annee)
            ->sum('montant');


        // Pas de calcul de pourcentage si aucun revenu.
        if ($totalRevenus <= 0) {
            return;
        }


        $pourcentage = ($totalDepenses / $totalRevenus) * 100;
        $solde = $totalRevenus - $totalDepenses;


        /*
         * Alerte à partir de 80 % du budget.
         */
        if ($pourcentage >= 80 && $pourcentage < 100) {

            $existe = NotificationBudget::where(
                'idUtilisateur',
                $userId
            )
                ->where('titre', 'Budget presque atteint')
                ->whereMonth('dateNotification', $mois)
                ->whereYear('dateNotification', $annee)
                ->exists();


            if (!$existe) {

                NotificationBudget::create([
                    'titre' => 'Budget presque atteint',
                    'message' =>
                        'Vous avez déjà utilisé 80 % de vos revenus ce mois-ci.',
                    'type' => 'Alerte',
                    'dateNotification' => now()->toDateString(),
                    'idUtilisateur' => $userId,
                ]);
            }
        }


        /*
         * Alerte lorsque les dépenses dépassent les revenus.
         */
        if ($totalDepenses > $totalRevenus) {

            $existe = NotificationBudget::where(
                'idUtilisateur',
                $userId
            )
                ->where('titre', 'Budget dépassé')
                ->whereMonth('dateNotification', $mois)
                ->whereYear('dateNotification', $annee)
                ->exists();


            if (!$existe) {

                NotificationBudget::create([
                    'titre' => 'Budget dépassé',
                    'message' =>
                        'Vos dépenses du mois ont dépassé vos revenus.',
                    'type' => 'Alerte',
                    'dateNotification' => now()->toDateString(),
                    'idUtilisateur' => $userId,
                ]);
            }


            /*
             * Notification du solde négatif.
             */
            $soldeExiste = NotificationBudget::where(
                'idUtilisateur',
                $userId
            )
                ->where('titre', 'Solde négatif')
                ->whereMonth('dateNotification', $mois)
                ->whereYear('dateNotification', $annee)
                ->exists();


            if (!$soldeExiste) {

                NotificationBudget::create([
                    'titre' => 'Solde négatif',
                    'message' =>
                        'Votre solde du mois est négatif de ' .
                        number_format(abs($solde), 2, ',', ' ') .
                        ' €.',
                    'type' => 'Alerte',
                    'dateNotification' => now()->toDateString(),
                    'idUtilisateur' => $userId,
                ]);
            }
        }
    }
}