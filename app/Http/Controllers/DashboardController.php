<?php

namespace App\Http\Controllers;

use App\Models\Depense;
use App\Models\Revenu;
use App\Models\DepenseRecurrente;
use App\Models\NotificationBudget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | TABLEAU DE BORD UTILISATEUR
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        // Récupère l'utilisateur actuellement connecté.
        $userId = auth()->id();

        /*
         * Récupère le mois et l'année choisis dans le filtre.
         * Si rien n'est choisi, le mois actuel est utilisé.
         */
        $mois = request('mois', now()->month);
        $annee = request('annee', now()->year);

        /*
         * Traite les dépenses récurrentes arrivées à échéance.
         */
        $this->traiterDepensesRecurrentes();


        /*
        |--------------------------------------------------------------------------
        | DEPENSES DU MOIS SELECTIONNE
        |--------------------------------------------------------------------------
        */

        $depenses = Depense::with('categorie')
            ->where('idUtilisateur', $userId)
            ->whereMonth('dateDepense', $mois)
            ->whereYear('dateDepense', $annee)
            ->orderBy('dateDepense', 'desc')
            ->take(5)
            ->get();


        // Calcule le total des dépenses du mois sélectionné.
        $totalDepenses = Depense::where(
                'idUtilisateur',
                $userId
            )
            ->whereMonth('dateDepense', $mois)
            ->whereYear('dateDepense', $annee)
            ->sum('montant');


        /*
        |--------------------------------------------------------------------------
        | REVENUS DU MOIS SELECTIONNE
        |--------------------------------------------------------------------------
        */

        $totalRevenus = Revenu::where(
                'idUtilisateur',
                $userId
            )
            ->whereMonth('dateRevenu', $mois)
            ->whereYear('dateRevenu', $annee)
            ->sum('montant');


        /*
        |--------------------------------------------------------------------------
        | CALCULS FINANCIERS
        |--------------------------------------------------------------------------
        */

        $budgetRestant = $totalRevenus - $totalDepenses;


        $depensesPourcentage = $totalRevenus > 0
            ? min(
                ($totalDepenses / $totalRevenus) * 100,
                100
            )
            : 0;


        $tauxEpargne = $totalRevenus > 0
            ? max(
                ($budgetRestant / $totalRevenus) * 100,
                0
            )
            : 0;


        /*
        |--------------------------------------------------------------------------
        | NOTIFICATIONS AUTOMATIQUES
        |--------------------------------------------------------------------------
        */

        /*
         * Les notifications budgétaires sont générées
         * seulement pour le véritable mois actuel.
         */
        if (
            $mois == now()->month &&
            $annee == now()->year
        ) {
            $this->genererNotificationsBudget(
                $userId,
                $totalRevenus,
                $totalDepenses,
                $budgetRestant,
                $depensesPourcentage
            );
        }


        /*
        |--------------------------------------------------------------------------
        | NOTIFICATIONS RECENTES
        |--------------------------------------------------------------------------
        */

        $notifications = NotificationBudget::where(
                'idUtilisateur',
                $userId
            )
            ->orderBy('dateNotification', 'desc')
            ->orderByDesc('idNotification')
            ->take(3)
            ->get();


        /*
        |--------------------------------------------------------------------------
        | CATEGORIE PRINCIPALE DU MOIS
        |--------------------------------------------------------------------------
        */

        $categoriePrincipale = Depense::select(
                'idCategorie',
                DB::raw('SUM(montant) as total')
            )
            ->with('categorie')
            ->where('idUtilisateur', $userId)
            ->whereMonth('dateDepense', $mois)
            ->whereYear('dateDepense', $annee)
            ->groupBy('idCategorie')
            ->orderByDesc('total')
            ->first();


        /*
        |--------------------------------------------------------------------------
        | AFFICHAGE
        |--------------------------------------------------------------------------
        */

        return view('dashboard', compact(
            'depenses',
            'notifications',
            'totalDepenses',
            'totalRevenus',
            'budgetRestant',
            'depensesPourcentage',
            'tauxEpargne',
            'categoriePrincipale',
            'mois',
            'annee'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | DEPENSES RECURRENTES AUTOMATIQUES
    |--------------------------------------------------------------------------
    */

    public function traiterDepensesRecurrentes()
    {
        $userId = auth()->id();

        /*
         * Transaction pour empêcher deux traitements
         * simultanés de générer la même dépense.
         */
        DB::transaction(function () use ($userId) {

            $recurrentes = DepenseRecurrente::where(
                    'idUtilisateur',
                    $userId
                )
                ->whereDate(
                    'prochaineDate',
                    '<=',
                    now()->toDateString()
                )
                ->lockForUpdate()
                ->get();


            foreach ($recurrentes as $recurrente) {

                $dateEcheance = Carbon::parse(
                    $recurrente->prochaineDate
                );


                /*
                 * Vérifie que la même dépense n'existe pas déjà
                 * pour cette échéance.
                 */
                $depense = Depense::firstOrCreate(
                    [
                        'idUtilisateur' => $userId,

                        'description' =>
                            $recurrente->nomDepenseRecurrente,

                        'montant' =>
                            $recurrente->montant,

                        'dateDepense' =>
                            $dateEcheance->toDateString(),

                        'idCategorie' =>
                            $recurrente->idCategorie,
                    ]
                );


                /*
                 * Crée la notification uniquement lorsque
                 * la dépense vient réellement d'être créée.
                 */
                if ($depense->wasRecentlyCreated) {

                    NotificationBudget::create([
                        'titre' =>
                            'Paiement récurrent effectué',

                        'message' =>
                            'La dépense récurrente "' .
                            $recurrente->nomDepenseRecurrente .
                            '" a été intégrée automatiquement au budget.',

                        'type' =>
                            'Paiement',

                        'dateNotification' =>
                            now()->toDateString(),

                        'idUtilisateur' =>
                            $userId,
                    ]);
                }


                /*
                 * Calcule la prochaine échéance.
                 */
                if ($recurrente->frequence === 'Mensuel') {

                    $dateEcheance->addMonth();

                } elseif (
                    $recurrente->frequence === 'Hebdomadaire'
                ) {

                    $dateEcheance->addWeek();

                } elseif (
                    $recurrente->frequence === 'Annuel'
                ) {

                    $dateEcheance->addYear();

                } else {

                    // Sécurité par défaut.
                    $dateEcheance->addMonth();
                }


                /*
                 * Si l'échéance calculée est encore dans le passé,
                 * on l'avance jusqu'à la prochaine date future.
                 *
                 * Cela évite de générer plusieurs anciennes copies
                 * lorsqu'une date récurrente est restée très en retard.
                 */
                while (
                    $dateEcheance->lt(
                        now()->startOfDay()
                    )
                ) {

                    if ($recurrente->frequence === 'Mensuel') {

                        $dateEcheance->addMonth();

                    } elseif (
                        $recurrente->frequence === 'Hebdomadaire'
                    ) {

                        $dateEcheance->addWeek();

                    } elseif (
                        $recurrente->frequence === 'Annuel'
                    ) {

                        $dateEcheance->addYear();

                    } else {

                        $dateEcheance->addMonth();
                    }
                }


                /*
                 * Enregistre la prochaine échéance.
                 */
                $recurrente->prochaineDate =
                    $dateEcheance->toDateString();

                $recurrente->save();
            }
        });
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATION DES NOTIFICATIONS BUDGETAIRES
    |--------------------------------------------------------------------------
    */

    private function genererNotificationsBudget(
        $userId,
        $totalRevenus,
        $totalDepenses,
        $budgetRestant,
        $depensesPourcentage
    ) {
        $moisActuel = now()->format('Y-m');


        /*
        |--------------------------------------------------------------------------
        | BUDGET PROCHE DE LA LIMITE
        |--------------------------------------------------------------------------
        */

        if (
            $totalRevenus > 0 &&
            $depensesPourcentage >= 80 &&
            $depensesPourcentage < 100
        ) {
            $this->creerNotificationSiAbsente(
                $userId,
                'budget-proche-' . $moisActuel,
                'Budget presque atteint',
                'Vous avez utilisé plus de 80 % de vos revenus ce mois-ci.',
                'Alerte'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BUDGET DEPASSE
        |--------------------------------------------------------------------------
        */

        if (
            $totalRevenus > 0 &&
            $totalDepenses > $totalRevenus
        ) {
            $this->creerNotificationSiAbsente(
                $userId,
                'budget-depasse-' . $moisActuel,
                'Budget dépassé',
                'Vos dépenses ont dépassé vos revenus pour ce mois.',
                'Alerte'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | SOLDE NEGATIF
        |--------------------------------------------------------------------------
        */

        if ($budgetRestant < 0) {

            $this->creerNotificationSiAbsente(
                $userId,
                'solde-negatif-' . $moisActuel,
                'Solde négatif',
                'Votre solde mensuel est actuellement négatif.',
                'Alerte'
            );
        }


        /*
        |--------------------------------------------------------------------------
        | BONNE EPARGNE
        |--------------------------------------------------------------------------
        */

        if (
            $totalRevenus > 0 &&
            $budgetRestant > 0 &&
            (($budgetRestant / $totalRevenus) * 100) >= 30
        ) {
            $this->creerNotificationSiAbsente(
                $userId,
                'bonne-epargne-' . $moisActuel,
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

    private function creerNotificationSiAbsente(
        $userId,
        $cle,
        $titre,
        $message,
        $type
    ) {
        $existe = NotificationBudget::where(
                'idUtilisateur',
                $userId
            )
            ->where('titre', $titre)
            ->whereMonth(
                'dateNotification',
                now()->month
            )
            ->whereYear(
                'dateNotification',
                now()->year
            )
            ->exists();


        // Ne recrée pas une notification déjà existante.
        if ($existe) {
            return;
        }


        NotificationBudget::create([
            'titre' => $titre,

            'message' => $message,

            'type' => $type,

            'dateNotification' =>
                now()->toDateString(),

            'idUtilisateur' =>
                $userId,
        ]);
    }
}