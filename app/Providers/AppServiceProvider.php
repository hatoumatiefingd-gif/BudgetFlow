<?php
namespace App\Providers;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
class AppServiceProvider extends ServiceProvider
{
   /**
    * Enregistre les services de l'application.
    */
   public function register(): void
   {
       //
   }
   /**
    * Configure l'application au démarrage.
    */
   public function boot(): void
   {
       /*
        * En production, force Laravel à générer
        * toutes les URLs avec HTTPS.
        */
       if (app()->environment('production')) {
           URL::forceScheme('https');
       }
   }
}