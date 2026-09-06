<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DepenseController;
use App\Http\Controllers\RevenuController;
use App\Http\Controllers\DepenseRecurrenteController;
use App\Http\Controllers\NotificationBudgetController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\ContactController;


/*
|--------------------------------------------------------------------------
| PAGE D'ACCUEIL
|--------------------------------------------------------------------------
*/

// Affiche la page d'accueil de BudgetFlow.
Route::get('/', function () {
    return view('welcome');
});


// Affiche la politique de confidentialité.
Route::get('/politique-confidentialite', function () {
    return view('politique-confidentialite');
})->name('politique.confidentialite');


/*
|--------------------------------------------------------------------------
| CONTACT
|--------------------------------------------------------------------------
*/

// Affiche le formulaire de contact.
Route::get(
    '/contact',
    [ContactController::class, 'create']
)->name('contact');


// Enregistre un message destiné à l'administrateur.
Route::post(
    '/contact',
    [ContactController::class, 'store']
)->name('contact.store');


/*
|--------------------------------------------------------------------------
| ESPACE UTILISATEUR
|--------------------------------------------------------------------------
| Toutes les routes de ce groupe nécessitent une connexion.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {

    /*
    |--------------------------------------------------------------------------
    | TABLEAU DE BORD UTILISATEUR
    |--------------------------------------------------------------------------
    */

    // Affiche le tableau de bord personnel de l'utilisateur.
    Route::get(
        '/dashboard',
        [DashboardController::class, 'index']
    )->name('dashboard');


    /*
    |--------------------------------------------------------------------------
    | CATEGORIES
    |--------------------------------------------------------------------------
    */

    // Affiche la page des catégories.
    Route::get('/categories', function () {
        return view('categories.index');
    })->name('categories.index');


    /*
    |--------------------------------------------------------------------------
    | REVENUS
    |--------------------------------------------------------------------------
    */

    // Affiche la liste des revenus.
    Route::get(
        '/revenus',
        [RevenuController::class, 'index']
    )->name('revenus.index');


    // Affiche le formulaire d'ajout d'un revenu.
    Route::get(
        '/revenus/create',
        [RevenuController::class, 'create']
    )->name('revenus.create');


    // Enregistre un nouveau revenu.
    Route::post(
        '/revenus',
        [RevenuController::class, 'store']
    )->name('revenus.store');


    // Affiche le formulaire de modification d'un revenu.
    Route::get(
        '/revenus/{id}/edit',
        [RevenuController::class, 'edit']
    )->name('revenus.edit');


    // Met à jour un revenu.
    Route::put(
        '/revenus/{id}',
        [RevenuController::class, 'update']
    )->name('revenus.update');


    // Supprime un revenu.
    Route::delete(
        '/revenus/{id}',
        [RevenuController::class, 'destroy']
    )->name('revenus.destroy');


    /*
    |--------------------------------------------------------------------------
    | DEPENSES
    |--------------------------------------------------------------------------
    */

    // Affiche la liste des dépenses.
    Route::get(
        '/depenses',
        [DepenseController::class, 'index']
    )->name('depenses.index');


    // Affiche le formulaire d'ajout d'une dépense.
    Route::get(
        '/depenses/create',
        [DepenseController::class, 'create']
    )->name('depenses.create');


    // Enregistre une nouvelle dépense.
    Route::post(
        '/depenses',
        [DepenseController::class, 'store']
    )->name('depenses.store');


    // Affiche le formulaire de modification d'une dépense.
    Route::get(
        '/depenses/{id}/edit',
        [DepenseController::class, 'edit']
    )->name('depenses.edit');


    // Met à jour une dépense.
    Route::put(
        '/depenses/{id}',
        [DepenseController::class, 'update']
    )->name('depenses.update');


    // Supprime une dépense.
    Route::delete(
        '/depenses/{id}',
        [DepenseController::class, 'destroy']
    )->name('depenses.destroy');


    /*
    |--------------------------------------------------------------------------
    | DEPENSES RECURRENTES
    |--------------------------------------------------------------------------
    */

    // Affiche la liste des dépenses récurrentes.
    Route::get(
        '/depenses-recurrentes',
        [DepenseRecurrenteController::class, 'index']
    )->name('depenses-recurrentes.index');


    // Affiche le formulaire d'ajout d'une dépense récurrente.
    Route::get(
        '/depenses-recurrentes/create',
        [DepenseRecurrenteController::class, 'create']
    )->name('depenses-recurrentes.create');


    // Enregistre une nouvelle dépense récurrente.
    Route::post(
        '/depenses-recurrentes',
        [DepenseRecurrenteController::class, 'store']
    )->name('depenses-recurrentes.store');


    // Affiche le formulaire de modification d'une dépense récurrente.
    Route::get(
        '/depenses-recurrentes/{id}/edit',
        [DepenseRecurrenteController::class, 'edit']
    )->name('depenses-recurrentes.edit');


    // Met à jour une dépense récurrente.
    Route::put(
        '/depenses-recurrentes/{id}',
        [DepenseRecurrenteController::class, 'update']
    )->name('depenses-recurrentes.update');


    // Supprime une dépense récurrente.
    Route::delete(
        '/depenses-recurrentes/{id}',
        [DepenseRecurrenteController::class, 'destroy']
    )->name('depenses-recurrentes.destroy');


    /*
    |--------------------------------------------------------------------------
    | NOTIFICATIONS UTILISATEUR
    |--------------------------------------------------------------------------
    */

    // Affiche les notifications de l'utilisateur.
    Route::get(
        '/notifications',
        [NotificationBudgetController::class, 'index']
    )->name('notifications.index');


    // Affiche le formulaire de création d'une notification.
    Route::get(
        '/notifications/create',
        [NotificationBudgetController::class, 'create']
    )->name('notifications.create');


    // Enregistre une nouvelle notification.
    Route::post(
        '/notifications',
        [NotificationBudgetController::class, 'store']
    )->name('notifications.store');


    // Affiche le formulaire de modification d'une notification.
    Route::get(
        '/notifications/{id}/edit',
        [NotificationBudgetController::class, 'edit']
    )->name('notifications.edit');


    // Met à jour une notification.
    Route::put(
        '/notifications/{id}',
        [NotificationBudgetController::class, 'update']
    )->name('notifications.update');


    // Supprime une notification appartenant à l'utilisateur connecté.
    Route::delete(
        '/notifications/{id}',
        [NotificationBudgetController::class, 'destroy']
    )->name('notifications.destroy');


    /*
    |--------------------------------------------------------------------------
    | PROFIL UTILISATEUR
    |--------------------------------------------------------------------------
    */

    // Affiche la page permettant de modifier son profil.
    Route::get(
        '/profil',
        [ProfileController::class, 'edit']
    )->name('profile.edit');


    // Met à jour le nom et l'adresse e-mail de l'utilisateur.
    Route::patch(
        '/profil',
        [ProfileController::class, 'update']
    )->name('profile.update');


    // Permet à l'utilisateur de supprimer son propre compte.
    Route::delete(
        '/profil',
        [ProfileController::class, 'destroy']
    )->name('profile.destroy');
});


/*
|--------------------------------------------------------------------------
| ESPACE ADMINISTRATEUR
|--------------------------------------------------------------------------
| Ces routes sont accessibles uniquement au compte administrateur.
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'role:admin'])
    ->prefix('admin')
    ->name('admin.')
    ->group(function () {

        /*
        |--------------------------------------------------------------------------
        | TABLEAU DE BORD ADMINISTRATEUR
        |--------------------------------------------------------------------------
        */

        // Affiche le tableau de bord administrateur.
        Route::get(
            '/dashboard',
            [AdminController::class, 'dashboard']
        )->name('dashboard');


        /*
        |--------------------------------------------------------------------------
        | UTILISATEURS
        |--------------------------------------------------------------------------
        */

        // Affiche la liste des utilisateurs.
        Route::get(
            '/utilisateurs',
            [AdminController::class, 'utilisateurs']
        )->name('utilisateurs');


        /*
        |--------------------------------------------------------------------------
        | MESSAGES DE CONTACT
        |--------------------------------------------------------------------------
        */

        // Affiche les messages envoyés depuis le formulaire de contact.
        Route::get(
            '/messages-contact',
            [AdminController::class, 'messagesContact']
        )->name('messages.contact');
    });


/*
|--------------------------------------------------------------------------
| ROUTES D'AUTHENTIFICATION
|--------------------------------------------------------------------------
*/

// Charge les routes de connexion, inscription et mot de passe.
require __DIR__ . '/auth.php';