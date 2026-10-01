<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

// Traite le formulaire de connexion.
public function store(LoginRequest $request): RedirectResponse

{

    // Vérifie l'e-mail et le mot de passe (voir app/Http/Requests/Auth/LoginRequest.php).
    $request->authenticate();

    // Crée un nouvel identifiant de session pour éviter le vol de session.
    $request->session()->regenerate();

   // L'administrateur est envoyé vers son tableau de bord.
   if (Auth::user()->role === 'admin') {

    return redirect()->route('admin.dashboard');

}

// Un utilisateur simple est envoyé vers son tableau de bord personnel.
return redirect()->route('dashboard');
 

}
 
    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        // Déconnecte l'utilisateur.
        Auth::guard('web')->logout();

        // Supprime la session et génère un nouveau jeton CSRF par sécurité.
        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
