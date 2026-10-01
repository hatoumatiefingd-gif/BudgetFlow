<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * Middleware de rôle : vérifie que l'utilisateur connecté a le bon rôle
 * avant de le laisser accéder à une page.
 *
 * Exemple dans routes/web.php : ->middleware(['auth', 'role:admin'])
 * Seul un compte avec le rôle "admin" peut alors ouvrir ces pages.
 */
class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role)
    {
        // Si personne n'est connecté, on renvoie vers la page de connexion.
        if (!Auth::check()) {
            return redirect('/login');
        }

        // Si le rôle ne correspond pas, on bloque avec une erreur 403 (accès interdit).
        if (Auth::user()->role !== $role) {
            abort(403, 'Accès non autorisé');
        }

        // Sinon, la requête continue normalement vers la page demandée.
        return $next($request);
    }
}
