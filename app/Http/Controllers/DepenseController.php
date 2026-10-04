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
        // Mois choisi dans le filtre (?mois=9 dans l'adresse), sinon le mois en cours.
        $mois = $request->mois ?: now()->month;

        // Même principe pour l'année : ?annee=2026, sinon l'année en cours.
        $annee = $request->annee ?: now()->year;

        $query = Depense::with('categorie') // charge la catégorie avec la dépense, pour afficher son nom
            ->where('idUtilisateur', auth()->id()) // seulement les dépenses de la personne connectée

            // Affiche uniquement les dépenses du mois choisi.
            ->whereMonth('dateDepense', $mois)
            ->whereYear('dateDepense', $annee);

        // Si une catégorie est choisie dans le filtre, on ajoute la condition.
        if ($request->categorie) {
            $query->where('idCategorie', $request->categorie);
        }

        $depenses = $query
            ->orderBy('dateDepense', 'desc') // la plus récente en premier
            ->get(); // exécute la requête SQL et renvoie la liste

        $categories = Categorie::all();

        return view('depenses.index', compact( // compact() envoie ces variables à la vue
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

        // Si une règle n'est pas respectée, Laravel revient au formulaire avec
        // les erreurs et la suite de la méthode n'est pas exécutée.
        $request->validate([
            'montant' => 'required|numeric|min:0', // obligatoire, un nombre, pas négatif
            'description' => 'required|string|max:255', // obligatoire, 255 caractères max (taille de la colonne)
            'dateDepense' => [
                'required',
                'date',
                'after_or_equal:' . $debutMois, // pas avant le 1er du mois
                'before_or_equal:' . $finMois, // pas après le dernier jour du mois
            ],
            'idCategorie' => 'required',
        ], [ // messages personnalisés pour les deux règles de date
            'dateDepense.after_or_equal' =>
                'Vous ne pouvez pas ajouter une dépense dans un mois déjà passé.',

            'dateDepense.before_or_equal' =>
                'La dépense doit appartenir au mois actuel.',
        ]);


        // INSERT INTO depense (...) : seuls les champs du $fillable sont acceptés.
        Depense::create([
            'montant' => $request->montant,
            'description' => $request->description,
            'dateDepense' => $request->dateDepense,
            'idUtilisateur' => auth()->id(), // vient de la session, jamais du formulaire
            'idCategorie' => $request->idCategorie,
        ]);


        // Message visible dans la page Notifications.
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


        // Crée une alerte si on approche ou dépasse le budget du mois.
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
     * Modifie une dépense (formulaire envoyé en PUT sur /depenses/{id}).
     */
    public function update(Request $request, $id)
    {
        // Même protection que edit() : on cherche seulement parmi MES dépenses,
        // sinon erreur 404.
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


        // Carbon transforme le texte "2026-10-04" en objet date,
        // pour pouvoir comparer les dates facilement (lt = avant, gt = après).
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
            // back() = retour au formulaire, withInput() = on garde ce qui a été tapé.
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


        // UPDATE depense SET ... WHERE idDepense = $id
        // (idUtilisateur n'est pas modifiable : la dépense reste à son propriétaire).
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
     * Supprime une dépense (formulaire envoyé en DELETE sur /depenses/{id}).
     */
    public function destroy($id)
    {
        // On vérifie d'abord que la dépense est bien à la personne connectée.
        $depense = Depense::where(
            'idUtilisateur',
            auth()->id()
        )->findOrFail($id);

        $depense->delete(); // DELETE FROM depense WHERE idDepense = $id

        return redirect()->route('depenses.index');
    }


    /**
     * Vérifie automatiquement l'état du budget mensuel.
     *
     * private : cette méthode n'a pas de route, elle est appelée
     * seulement depuis ce contrôleur (après store et update).
     * Elle crée au maximum une alerte de chaque type par mois.
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
            ->sum('montant'); // SELECT SUM(montant) ... : le total en une seule requête


        // Total réel des revenus du mois.
        $totalRevenus = Revenu::where(
            'idUtilisateur',
            $userId
        )
            ->whereMonth('dateRevenu', $mois)
            ->whereYear('dateRevenu', $annee)
            ->sum('montant');


        // Sans revenu, on ne peut pas calculer de pourcentage (division par zéro).
        if ($totalRevenus <= 0) {
            return;
        }


        // Exemple : 850 € dépensés sur 1 000 € de revenus = 85 %.
        $pourcentage = ($totalDepenses / $totalRevenus) * 100;
        $solde = $totalRevenus - $totalDepenses;


        /*
         * Alerte à partir de 80 % du budget.
         */
        if ($pourcentage >= 80 && $pourcentage < 100) {

            // exists() renvoie vrai ou faux : on ne crée pas la même alerte
            // deux fois dans le même mois.
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
                        number_format(abs($solde), 2, ',', ' ') . // ex : 150,00 (abs enlève le signe moins)
                        ' €.',
                    'type' => 'Alerte',
                    'dateNotification' => now()->toDateString(),
                    'idUtilisateur' => $userId,
                ]);
            }
        }
    }
}