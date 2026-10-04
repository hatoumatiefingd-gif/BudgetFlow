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
        // request('mois', valeur) : lit ?mois= dans l'adresse, sinon prend la valeur par défaut.
        $mois = request('mois', now()->month);
        $annee = request('annee', now()->year);

        /*
         * Avant de calculer les totaux, on ajoute les abonnements dont la date
         * est arrivée : sinon ils n'apparaîtraient pas dans les chiffres du mois.
         * $this = ce contrôleur, on appelle sa propre méthode (plus bas).
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
            ->take(5) // LIMIT 5 : seulement les 5 dernières pour le bloc "Dernières dépenses"
            ->get();


        // Total des dépenses du mois : SELECT SUM(montant) FROM depense WHERE ...
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

        // Ce qu'il reste à dépenser. Peut être négatif si on a dépensé plus que gagné.
        $budgetRestant = $totalRevenus - $totalDepenses;


        // Part des revenus déjà dépensée, en %. min(..., 100) bloque à 100 %
        // pour que la barre ne dépasse pas. Sans revenu : 0 (pas de division par zéro).
        $depensesPourcentage = $totalRevenus > 0
            ? min(
                ($totalDepenses / $totalRevenus) * 100,
                100
            )
            : 0;


        // Part des revenus qui reste (épargne). max(..., 0) : jamais en dessous de 0 %.
        // Exemple : 1 000 € gagnés, 700 € dépensés -> 300 € restants -> 30 %.
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
         * Les alertes sont créées seulement quand on regarde le mois en cours.
         * Si on filtre sur un ancien mois, on ne crée pas d'alerte "budget dépassé"
         * pour un mois déjà terminé.
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
            ->orderByDesc('idNotification') // même date : la plus récemment créée d'abord
            ->take(3) // les 3 dernières seulement, pour le tableau de bord
            ->get();


        /*
        |--------------------------------------------------------------------------
        | CATEGORIE PRINCIPALE DU MOIS
        |--------------------------------------------------------------------------
        */

        /*
         * La catégorie où l'on a le plus dépensé ce mois-ci. En SQL :
         * SELECT idCategorie, SUM(montant) AS total FROM depense
         * WHERE idUtilisateur = ... GROUP BY idCategorie ORDER BY total DESC LIMIT 1
         * DB::raw permet d'écrire le SUM(montant) tel quel dans la requête.
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
            ->first(); // first() = seulement la première ligne (la plus grosse catégorie)


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
         * Transaction = tout ou rien. Créer la dépense, la notification et
         * avancer la date se font ensemble : si une étape plante, MySQL annule
         * tout (rollback). On n'a donc jamais une dépense créée sans que la date
         * ait avancé, ce qui la recréerait à la prochaine visite.
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
                ->lockForUpdate() // verrouille ces lignes : si la page est ouverte deux fois en même temps, le 2e traitement attend
                ->get();


            // Une boucle pour chaque abonnement dont la date est aujourd'hui ou passée.
            foreach ($recurrentes as $recurrente) {

                $dateEcheance = Carbon::parse(
                    $recurrente->prochaineDate
                );


                /*
                 * firstOrCreate = "trouve ou crée" : cherche une dépense avec exactement
                 * ces valeurs ; si elle existe déjà, on la récupère au lieu d'en créer
                 * une deuxième. Protection supplémentaire contre les doublons.
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
                 * Calcule la prochaine échéance selon la fréquence choisie
                 * à la création (Mensuel, Hebdomadaire ou Annuel).
                 * addMonth / addWeek / addYear viennent de Carbon.
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

                $recurrente->save(); // UPDATE depenserecurrente SET prochaineDate = ...
            }
        }); // fin de la transaction : tout est validé (commit) ici
    }


    /*
    |--------------------------------------------------------------------------
    | GENERATION DES NOTIFICATIONS BUDGETAIRES
    |--------------------------------------------------------------------------
    */

    // Crée les alertes du mois selon les chiffres calculés dans index().
    // Mêmes seuils que le graphique : 80 % (orange) et 100 % (rouge).
    private function genererNotificationsBudget(
        $userId,
        $totalRevenus,
        $totalDepenses,
        $budgetRestant,
        $depensesPourcentage
    ) {
        $moisActuel = now()->format('Y-m'); // ex : "2026-10"


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

    // Crée une notification seulement si elle n'existe pas déjà ce mois-ci.
    // Le paramètre $cle (ex : "budget-proche-2026-10") n'est pas utilisé pour
    // l'instant : la vérification se fait avec le titre et le mois.
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