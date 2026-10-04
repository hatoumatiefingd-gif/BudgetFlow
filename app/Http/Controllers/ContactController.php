<?php
namespace App\Http\Controllers;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
// Formulaire "Nous contacter" (lien en bas de la page d'accueil).
// Les routes /contact sont publiques : pas besoin d'être connecté pour écrire.
// L'admin lit les messages dans AdminController::messagesContact().
class ContactController extends Controller
{
   /**
    * Affiche le formulaire de contact.
    */
   public function create()
   {
       return view('contact');
   }
   /**
    * Enregistre le message envoyé à l'administrateur.
    */
   public function store(Request $request)
   {
       // email : vérifie le format (quelque chose@domaine). max:2000 évite
       // qu'on envoie un texte énorme dans la base.
       $request->validate([
           'name' => 'required|string|max:255',
           'email' => 'required|email|max:255',
           'message' => 'required|string|max:2000',
       ]);
       // Enregistre le message dans la base de données.
       ContactMessage::create([
           'name' => $request->name,
           'email' => $request->email,
           'message' => $request->message,
           'lu' => false, // nouveau message = pas encore lu par l'admin
       ]);
       // Retour sur le formulaire avec un message de confirmation (affiché une seule fois).
       return redirect()
           ->route('contact')
           ->with('success', 'Votre message a bien été envoyé.');
   }
}