<?php
namespace App\Http\Controllers;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
class ProfileController extends Controller
{
   /*
   |--------------------------------------------------------------------------
   | AFFICHAGE DU PROFIL
   |--------------------------------------------------------------------------
   */
   public function edit(Request $request): View
   {
       // Récupère l'utilisateur actuellement connecté.
       $user = $request->user();
       // Affiche la page de modification du profil.
       return view('profile.edit', compact('user'));
   }

   /*
   |--------------------------------------------------------------------------
   | MODIFICATION DU PROFIL
   |--------------------------------------------------------------------------
   */
   public function update(ProfileUpdateRequest $request): RedirectResponse
   {
       // Met à jour les informations validées de l'utilisateur.
       $request->user()->fill($request->validated());
       // Si l'adresse e-mail change, elle redevient non vérifiée.
       if ($request->user()->isDirty('email')) {
           $request->user()->email_verified_at = null;
       }
       // Enregistre les modifications.
       $request->user()->save();
       // Retourne vers la page profil avec un message de confirmation.
       return Redirect::route('profile.edit')
           ->with('status', 'profile-updated');
   }

   /*
   |--------------------------------------------------------------------------
   | SUPPRESSION DU PROPRE COMPTE
   |--------------------------------------------------------------------------
   */
   public function destroy(Request $request): RedirectResponse
   {
       // Vérifie le mot de passe avant de supprimer le compte.
       $request->validateWithBag('userDeletion', [
           'password' => ['required', 'current_password'],
       ]);
       // Récupère l'utilisateur connecté.
       $user = $request->user();
       // Déconnecte l'utilisateur.
       Auth::logout();
       // Supprime son compte.
       $user->delete();
       // Sécurise la session.
       $request->session()->invalidate();
       $request->session()->regenerateToken();
       return Redirect::to('/');
   }
}