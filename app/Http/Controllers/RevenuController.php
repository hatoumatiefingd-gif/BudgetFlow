<?php

namespace App\Http\Controllers;

use App\Models\Revenu;
use App\Models\Depense;
use App\Models\NotificationBudget;
use Illuminate\Http\Request;
use Carbon\Carbon;

class RevenuController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTE DES REVENUS
    |--------------------------------------------------------------------------
    */

    public function index(Request $request)
    {
        // Utilise le mois actuel si aucun mois n'est sélectionné.
        $mois = $request->mois ?: now()->month;

        // Utilise l'année actuelle si aucune année n'est sélectionnée.
        $annee = $request->annee ?: now()->year;

        // Récupère uniquement les revenus de l'utilisateur connecté.
        $revenus = Revenu::where('idUtilisateur', auth()->id())
            ->whereMonth('dateRevenu', $mois)
            ->whereYear('dateRevenu', $annee)
            ->orderBy('dateRevenu', 'desc')
            ->get();

        return view('revenus.index', compact(
            'revenus',
            'mois',
            'annee'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | FORMULAIRE D'AJOUT
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('revenus.create');
    }


    /*
    |--------------------------------------------------------------------------
    | ENREGISTREMENT D'UN REVENU
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        // Début et fin du mois actuel.
        $debutMois = now()->startOfMonth()->toDateString();
        $finMois = now()->endOfMonth()->toDateString();

        // Vérifie les données saisies.
        $request->validate(
            [
                'montant' => 'required|numeric|min:0',
                'source' => 'required|string|max:255',

                // Le revenu doit appartenir au mois actuel.
                'dateRevenu' => [
                    'required',
                    'date',
                    'after_or_equal:' . $debutMois,
                    'before_or_equal:' . $finMois,
                ],
            ],
            [
                'dateRevenu.after_or_equal' =>
                    'Vous ne pouvez pas ajouter un revenu dans un mois déjà passé.',

                'dateRevenu.before_or_equal' =>
                    'Le revenu doit appartenir au mois actuel.',
            ]
        );

        // Enregistre le revenu.
        Revenu::create([
            'montant' => $request->montant,
            'source' => $request->source,
            'dateRevenu' => $request->dateRevenu,
            'idUtilisateur' => auth()->id(),
        ]);

        // Vérifie la nouvelle situation du budget.
        $this->verifierBudget();

        return redirect()
            ->route('revenus.index')
            ->with('success', 'Revenu ajouté avec succès.');
    }


    /*
    |--------------------------------------------------------------------------
    | FORMULAIRE DE MODIFICATION
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        // Récupère uniquement un revenu appartenant à l'utilisateur.
        $revenu = Revenu::where('idUtilisateur', auth()->id())
            ->findOrFail($id);

        return view('revenus.edit', compact('revenu'));
    }


    /*
    |--------------------------------------------------------------------------
    | MODIFICATION D'UN REVENU
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        // Recherche le revenu.
        $revenu = Revenu::where('idUtilisateur', auth()->id())
            ->findOrFail($id);

        // Vérifie les données.
        $request->validate([
            'montant' => 'required|numeric|min:0',
            'source' => 'required|string|max:255',
            'dateRevenu' => 'required|date',
        ]);

        // Ancienne et nouvelle date.
        $ancienneDate = Carbon::parse($revenu->dateRevenu);
        $nouvelleDate = Carbon::parse($request->dateRevenu);

        /*
         * Si le revenu appartient à un ancien mois,
         * sa date historique ne peut plus être changée.
         */
        if (
            $ancienneDate->lt(now()->startOfMonth()) &&
            !$nouvelleDate->isSameDay($ancienneDate)
        ) {
            return back()
                ->withErrors([
                    'dateRevenu' =>
                        'La date d’un revenu d’un ancien mois ne peut plus être modifiée.'
                ])
                ->withInput();
        }

        /*
         * Si le revenu appartient au mois actuel,
         * il doit rester dans le mois actuel.
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
                    'dateRevenu' =>
                        'La date doit appartenir au mois actuel.'
                ])
                ->withInput();
        }

        // Met à jour le revenu.
        $revenu->update([
            'montant' => $request->montant,
            'source' => $request->source,
            'dateRevenu' => $request->dateRevenu,
        ]);

        // Recalcule la situation budgétaire.
        $this->verifierBudget();

        return redirect()
            ->route('revenus.index')
            ->with('success', 'Revenu modifié avec succès.');
    }


    /*
    |--------------------------------------------------------------------------
    | SUPPRESSION D'UN REVENU
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        // Recherche le revenu de l'utilisateur connecté.
        $revenu = Revenu::where('idUtilisateur', auth()->id())
            ->findOrFail($id);

        // Supprime le revenu.
        $revenu->delete();

        // Recalcule le budget après suppression.
        $this->verifierBudget();

        return redirect()
            ->route('revenus.index')
            ->with('success', 'Revenu supprimé avec succès.');
    }


    /*
    |--------------------------------------------------------------------------
    | VERIFICATION DU BUDGET MENSUEL
    |--------------------------------------------------------------------------
    */

    private function verifierBudget()
    {
        $userId = auth()->id();
        $mois = now()->month;
        $annee = now()->year;

        // Calcule les revenus du mois actuel.
        $totalRevenus = Revenu::where('idUtilisateur', $userId)
            ->whereMonth('dateRevenu', $mois)
            ->whereYear('dateRevenu', $annee)
            ->sum('montant');

        // Calcule les dépenses du mois actuel.
        $totalDepenses = Depense::where('idUtilisateur', $userId)
            ->whereMonth('dateDepense', $mois)
            ->whereYear('dateDepense', $annee)
            ->sum('montant');

        // Aucun calcul de pourcentage si aucun revenu.
        if ($totalRevenus <= 0) {
            return;
        }

        // Calcule le solde et le pourcentage utilisé.
        $solde = $totalRevenus - $totalDepenses;
        $pourcentage = ($totalDepenses / $totalRevenus) * 100;


        /*
        |--------------------------------------------------------------------------
        | BUDGET PRESQUE ATTEINT
        |--------------------------------------------------------------------------
        */

        if ($pourcentage >= 80 && $pourcentage < 100) {

            $this->creerNotificationMensuelleSiAbsente(
                'Budget presque atteint',
                'Vous avez déjà utilisé ' .
                    round($pourcentage) .
                    ' % de vos revenus ce mois-ci.',
                'Alerte'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BUDGET DEPASSE
        |--------------------------------------------------------------------------
        */

        if ($totalDepenses > $totalRevenus) {

            $this->creerNotificationMensuelleSiAbsente(
                'Budget dépassé',
                'Vos dépenses du mois ont dépassé vos revenus.',
                'Alerte'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SOLDE NEGATIF
        |--------------------------------------------------------------------------
        */

        if ($solde < 0) {

            $this->creerNotificationMensuelleSiAbsente(
                'Solde négatif',
                'Votre solde du mois est négatif de ' .
                    number_format(abs($solde), 2, ',', ' ') .
                    ' €.',
                'Alerte'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BONNE EPARGNE
        |--------------------------------------------------------------------------
        */

        $tauxEpargne = ($solde / $totalRevenus) * 100;

        if ($solde > 0 && $tauxEpargne >= 30) {

            $this->creerNotificationMensuelleSiAbsente(
                'Bonne épargne',
                'Vous avez conservé au moins 30 % de vos revenus ce mois-ci.',
                'Réussite'
            );
        }
    }


    /*
    |--------------------------------------------------------------------------
    | EVITE LES NOTIFICATIONS EN DOUBLE
    |--------------------------------------------------------------------------
    */

    private function creerNotificationMensuelleSiAbsente(
        $titre,
        $message,
        $type
    ) {
        // Vérifie si cette notification existe déjà ce mois-ci.
        $notificationExiste = NotificationBudget::where(
                'idUtilisateur',
                auth()->id()
            )
            ->where('titre', $titre)
            ->whereMonth('dateNotification', now()->month)
            ->whereYear('dateNotification', now()->year)
            ->exists();

        // Ne crée pas la même notification plusieurs fois.
        if ($notificationExiste) {
            return;
        }

        // Enregistre la notification.
        NotificationBudget::create([
            'titre' => $titre,
            'message' => $message,
            'type' => $type,
            'dateNotification' => now()->toDateString(),
            'idUtilisateur' => auth()->id(),
        ]);
    }
}