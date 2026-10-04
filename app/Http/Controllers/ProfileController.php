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
       // ProfileUpdateRequest valide les données avant d'arriver ici.
       // fill() remplit le nom et l'e-mail, sans encore enregistrer.
       $request->user()->fill($request->validated());
       // isDirty('email') = "l'e-mail a-t-il été modifié ?". Si oui, il redevient
       // non vérifié.
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
       // current_password : il faut taper son mot de passe actuel pour confirmer.
       // Ça évite qu'une autre personne supprime le compte sur un ordinateur resté connecté.
       $request->validateWithBag('userDeletion', [
           'password' => ['required', 'current_password'],
       ]);
       // Récupère l'utilisateur connecté.
       $user = $request->user();
       // Déconnecte l'utilisateur.
       Auth::logout();
       // DELETE FROM users WHERE id = ... Grâce au onDelete('cascade') des clés
       // étrangères, ses dépenses, revenus, abonnements et notifications sont
       // supprimés en même temps. C'est le droit à l'effacement du RGPD.
       $user->delete();
       // invalidate() vide la session, regenerateToken() crée un nouveau jeton CSRF.
       $request->session()->invalidate();
       $request->session()->regenerateToken();
       return Redirect::to('/');
   }
}