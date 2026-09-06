<?php
namespace App\Http\Controllers;
use App\Models\ContactMessage;
use Illuminate\Http\Request;
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
       // Vérifie les informations saisies dans le formulaire.
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
           'lu' => false,
       ]);
       return redirect()
           ->route('contact')
           ->with('success', 'Votre message a bien été envoyé.');
   }
}