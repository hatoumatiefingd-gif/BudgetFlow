<?php

namespace App\Http\Controllers;

use App\Models\NotificationBudget;
use Illuminate\Http\Request;

class NotificationBudgetController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LISTE DES NOTIFICATIONS
    |--------------------------------------------------------------------------
    */

    public function index()
    {
        // Récupère uniquement les notifications de l'utilisateur connecté.
        $notifications = NotificationBudget::where(
                'idUtilisateur',
                auth()->id()
            )
            ->orderBy('dateNotification', 'desc')
            ->orderByDesc('idNotification')
            ->get();

        return view(
            'notifications.index',
            compact('notifications')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | CREATION
    |--------------------------------------------------------------------------
    */

    public function create()
    {
        return view('notifications.create');
    }


    /*
    |--------------------------------------------------------------------------
    | ENREGISTREMENT
    |--------------------------------------------------------------------------
    */

    public function store(Request $request)
    {
        // Vérifie les informations saisies.
        $request->validate([
            'titre' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|max:50',
            'dateNotification' => 'required|date',
        ]);

        // Enregistre la notification pour l'utilisateur connecté.
        NotificationBudget::create([
            'titre' => $request->titre,
            'message' => $request->message,
            'type' => $request->type,
            'dateNotification' => $request->dateNotification,
            'idUtilisateur' => auth()->id(),
        ]);

        return redirect()
            ->route('notifications.index')
            ->with('success', 'Notification ajoutée avec succès.');
    }


    /*
    |--------------------------------------------------------------------------
    | MODIFICATION
    |--------------------------------------------------------------------------
    */

    public function edit($id)
    {
        // Recherche uniquement une notification appartenant à l'utilisateur.
        $notification = NotificationBudget::where(
                'idUtilisateur',
                auth()->id()
            )
            ->findOrFail($id);

        return view(
            'notifications.edit',
            compact('notification')
        );
    }


    /*
    |--------------------------------------------------------------------------
    | MISE A JOUR
    |--------------------------------------------------------------------------
    */

    public function update(Request $request, $id)
    {
        // Vérifie les nouvelles informations.
        $request->validate([
            'titre' => 'required|string|max:255',
            'message' => 'required|string',
            'type' => 'required|string|max:50',
            'dateNotification' => 'required|date',
        ]);

        // Recherche uniquement la notification de l'utilisateur.
        $notification = NotificationBudget::where(
                'idUtilisateur',
                auth()->id()
            )
            ->findOrFail($id);

        // Enregistre les modifications.
        $notification->update([
            'titre' => $request->titre,
            'message' => $request->message,
            'type' => $request->type,
            'dateNotification' => $request->dateNotification,
        ]);

        return redirect()
            ->route('notifications.index')
            ->with('success', 'Notification modifiée avec succès.');
    }


    /*
    |--------------------------------------------------------------------------
    | SUPPRESSION
    |--------------------------------------------------------------------------
    */

    public function destroy($id)
    {
        // Empêche la suppression d'une notification appartenant à un autre compte.
        $notification = NotificationBudget::where(
                'idUtilisateur',
                auth()->id()
            )
            ->findOrFail($id);

        // Supprime définitivement la notification.
        $notification->delete();

        return redirect()
            ->route('notifications.index')
            ->with('success', 'Notification supprimée avec succès.');
    }
}