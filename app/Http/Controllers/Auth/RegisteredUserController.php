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

    $request->validate([

        'name' => ['required', 'string', 'max:255'],

        'email' => [

            'required',

            'string',

            'lowercase',

            'email',

            'max:255',

            'unique:'.User::class,

        ],

        'password' => [

            'required',

            'confirmed',

            Rules\Password::min(8)

                ->mixedCase()

                ->numbers()

                ->symbols(),

        ],

    ]);

    $user = User::create([

        'name' => $request->name,

        'email' => $request->email,

        'password' => Hash::make($request->password),

    ]);
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
    NotificationAdmin::create([

        'titre' => 'Nouvel utilisateur',

        'message' => $user->name . ' vient de créer un compte.',

        'type' => 'Utilisateur',

        'dateNotification' => now()->toDateString(),

    ]);

    event(new Registered($user));

    Auth::login($user);

    return redirect(route('dashboard', absolute: false));

}
 
}
