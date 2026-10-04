<?php
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Symfony\Component\HttpFoundation\Request;
return Application::configure(basePath: dirname(__DIR__))
   ->withRouting(
       web: __DIR__.'/../routes/web.php',
       commands: __DIR__.'/../routes/console.php',
       health: '/up',
   )
   ->withMiddleware(function (Middleware $middleware): void {
       // En ligne, Railway reçoit d'abord la connexion HTTPS puis la transmet à
       // Laravel (c'est un proxy). Sans cette ligne, Laravel croirait que le site
       // est en HTTP et générerait des liens http:// au lieu de https://.
       $middleware->trustProxies(
           at: '*',
           headers:
               Request::HEADER_X_FORWARDED_FOR |
               Request::HEADER_X_FORWARDED_HOST |
               Request::HEADER_X_FORWARDED_PORT |
               Request::HEADER_X_FORWARDED_PROTO
       );
       // Alias : quand j'écris 'role:admin' dans routes/web.php, Laravel sait qu'il
       // doit utiliser ma classe RoleMiddleware. Le texte après ':' devient $role.
       $middleware->alias([
           'role' => \App\Http\Middleware\RoleMiddleware::class,
       ]);
   })
   ->withExceptions(function (Exceptions $exceptions): void {
       //
   })
   ->create();