<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\NotificationAdmin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Illuminate\Support\Facades\Mail;
 
class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
   public function store(Request $request): RedirectResponse

{

    // Vérifie les champs du formulaire d'inscription.
    // "unique" empêche de créer deux comptes avec le même e-mail.
    $request->validate([

        'name' => ['required', 'string', 'max:255'],

        'email' => [

            'required',

            'string',

            'lowercase',

            'email',

            'max:255',

            'unique:'.User::class, // refuse un e-mail déjà utilisé par un autre compte

        ],

        'password' => [

            'required',

            'confirmed',

            // Mot de passe sécurisé : 8 caractères minimum, majuscule + minuscule,
            // au moins un chiffre et un caractère spécial. "confirmed" vérifie
            // que les deux mots de passe saisis sont identiques.
            Rules\Password::min(8)

                ->mixedCase()

                ->numbers()

                ->symbols(),

        ],

    ]);

    // Crée le compte. Le mot de passe est haché (jamais stocké en clair).
    // Le rôle n'est pas indiqué : la base met "utilisateur" par défaut.
    $user = User::create([

        'name' => $request->name,

        'email' => $request->email,

        // Hash::make : bcrypt transforme le mot de passe en empreinte illisible
        // (ex : $2y$12$...). Impossible de revenir au mot de passe d'origine.
        'password' => Hash::make($request->password),

    ]);
// Envoie un e-mail de bienvenue au nouvel utilisateur.
Mail::raw(
   "Bonjour ".$user->name.",\n\n".
   "Bienvenue sur BudgetFlow !\n".
   "Votre compte a bien été créé.\n\n".
   "Vous pouvez maintenant vous connecter et gérer vos dépenses et revenus.\n\n".
   "À bientôt sur BudgetFlow.",
   function ($mail) use ($user) {
       $mail->to($user->email)
            ->subject('Bienvenue sur BudgetFlow');
   }
);
    // Prévient l'administrateur qu'un nouveau compte a été créé.
    NotificationAdmin::create([

        'titre' => 'Nouvel utilisateur',

        'message' => $user->name . ' vient de créer un compte.',

        'type' => 'Utilisateur',

        'dateNotification' => now()->toDateString(),

    ]);

    // Déclenche l'événement Laravel "Registered" (envoi de l'e-mail de vérification si activé).
    event(new Registered($user));

    // Connecte automatiquement l'utilisateur puis l'envoie vers son tableau de bord.
    Auth::login($user);

    return redirect(route('dashboard', absolute: false));

}
 
}
