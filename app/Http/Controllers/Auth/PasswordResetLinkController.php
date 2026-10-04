<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /**
     * Affiche le formulaire "Mot de passe oublié" (on tape son e-mail).
     */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Envoie l'e-mail avec le lien de réinitialisation.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Laravel cherche le compte avec cet e-mail, crée un jeton secret (rangé dans
        // la table password_reset_tokens) et envoie un e-mail avec un lien qui contient
        // ce jeton. Le lien expire après 60 minutes et on ne peut redemander un lien
        // qu'une fois par minute (réglages expire et throttle dans config/auth.php).
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Lien envoyé : message de confirmation. Sinon : retour avec l'erreur
        // (ex : "Veuillez patienter avant de réessayer").
        return $status == Password::RESET_LINK_SENT
                    ? back()->with('status', __($status))
                    : back()->withInput($request->only('email'))
                        ->withErrors(['email' => __($status)]);
    }
}
