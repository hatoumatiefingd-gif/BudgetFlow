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
         * Les dépenses récurrentes sont traitées selon la vraie date actuelle.
         * Le filtre du tableau de bord ne modifie donc pas leur fonctionnement.
         */
        $this->traiterDepensesRecurrentes();


        /*
        |--------------------------------------------------------------------------
        | DEPENSES DU MOIS SELECTIONNE
        |--------------------------------------------------------------------------
        */

        // Récupère les 5 dernières dépenses du mois sélectionné.
        $depenses = Depense::with('categorie')
            ->where('idUtilisateur', $userId)
            ->whereMonth('dateDepense', $mois)
            ->whereYear('dateDepense', $annee)
            ->orderBy('dateDepense', 'desc')
            ->take(5)
            ->get();


        // Calcule le total réel des dépenses du mois sélectionné.
        $totalDepenses = Depense::where('idUtilisateur', $userId)
            ->whereMonth('dateDepense', $mois)
            ->whereYear('dateDepense', $annee)
            ->sum('montant');


        /*
        |--------------------------------------------------------------------------
        | REVENUS DU MOIS SELECTIONNE
        |--------------------------------------------------------------------------
        */

        // Calcule le total réel des revenus du mois sélectionné.
        $totalRevenus = Revenu::where('idUtilisateur', $userId)
            ->whereMonth('dateRevenu', $mois)
            ->whereYear('dateRevenu', $annee)
            ->sum('montant');


        /*
        |--------------------------------------------------------------------------
        | CALCULS FINANCIERS
        |--------------------------------------------------------------------------
        */

        // Calcule le montant restant pour le mois sélectionné.
        $budgetRestant = $totalRevenus - $totalDepenses;


        // Calcule le pourcentage des revenus déjà dépensés.
        $depensesPourcentage = $totalRevenus > 0
            ? min(($totalDepenses / $totalRevenus) * 100, 100)
            : 0;


        // Calcule le taux d'épargne réel.
        $tauxEpargne = $totalRevenus > 0
            ? max(($budgetRestant / $totalRevenus) * 100, 0)
            : 0;


        /*
        |--------------------------------------------------------------------------
        | NOTIFICATIONS AUTOMATIQUES
        |--------------------------------------------------------------------------
        */

        /*
         * Les notifications automatiques sont créées uniquement
         * pour le vrai mois actuel.
         *
         * Ainsi, consulter un ancien mois ne crée aucune nouvelle alerte.
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

        // Récupère les 3 dernières notifications de l'utilisateur.
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

        // Recherche la catégorie dans laquelle l'utilisateur dépense le plus.
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

            // Nécessaires pour le filtre mois / année dans la vue.
            'mois',
            'annee'
        ));
    }


    /*
    |--------------------------------------------------------------------------
    | DEPENSES RECURRENTES AUTOMATIQUES
    |--------------------------------------------------------------------------
    */
// Traite automatiquement toutes les dépenses récurrentes arrivées à échéance.
public function traiterDepensesRecurrentes()
    {
        // Récupère l'utilisateur actuellement connecté.
        $userId = auth()->id();


        // Cherche les dépenses récurrentes arrivées à échéance.
        $recurrentes = DepenseRecurrente::where(
                'idUtilisateur',
                $userId
            )
            ->whereDate(
                'prochaineDate',
                '<=',
                now()->toDateString()
            )
            ->get();


        foreach ($recurrentes as $recurrente) {

            /*
             * Ajoute automatiquement la dépense récurrente
             * aux dépenses normales.
             */
            Depense::create([
                'montant' => $recurrente->montant,
                'description' =>
                    $recurrente->nomDepenseRecurrente,
                'dateDepense' =>
                    $recurrente->prochaineDate,
                'idUtilisateur' => $userId,
                'idCategorie' =>
                    $recurrente->idCategorie,
            ]);


            // Enregistre une notification réelle.
            NotificationBudget::create([
                'titre' =>
                    'Paiement récurrent effectué',

                'message' =>
                    'La dépense récurrente "' .
                    $recurrente->nomDepenseRecurrente .
                    '" a été intégrée automatiquement au budget.',

                'type' => 'Paiement',

                'dateNotification' =>
                    now()->toDateString(),

                'idUtilisateur' => $userId,
            ]);


            /*
             * Calcule la prochaine date
             * selon la fréquence choisie.
             */
            $date = Carbon::parse(
                $recurrente->prochaineDate
            );


            if ($recurrente->frequence === 'Mensuel') {

                $recurrente->prochaineDate =
                    $date->addMonth()->toDateString();

            } elseif (
                $recurrente->frequence === 'Hebdomadaire'
            ) {

                $recurrente->prochaineDate =
                    $date->addWeek()->toDateString();

            } elseif (
                $recurrente->frequence === 'Annuel'
            ) {

                $recurrente->prochaineDate =
                    $date->addYear()->toDateString();

            } else {

                // Par défaut : fréquence mensuelle.
                $recurrente->prochaineDate =
                    $date->addMonth()->toDateString();
            }


            // Enregistre la nouvelle échéance.
            $recurrente->save();
        }
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
        /*
         * Les notifications concernent toujours
         * le véritable mois actuel.
         */
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
        /*
         * Vérifie si une notification identique
         * existe déjà pendant le mois actuel.
         */
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


        // Si elle existe déjà, on ne la recrée pas.
        if ($existe) {
            return;
        }


        // Enregistre la nouvelle notification.
        NotificationBudget::create([
            'titre' => $titre,
            'message' => $message,
            'type' => $type,
            'dateNotification' =>
                now()->toDateString(),
            'idUtilisateur' => $userId,
        ]);
    }
}