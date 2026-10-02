<?php

/*
 * Messages d'erreur de validation en français.
 * :attribute est remplacé par le nom du champ (voir « attributes » en bas).
 * Les règles absentes de ce fichier gardent le message anglais par défaut.
 */

return [

    'required' => 'Le champ :attribute est obligatoire.',
    'string' => 'Le champ :attribute doit être un texte.',
    'numeric' => 'Le champ :attribute doit être un nombre.',
    'date' => 'Le champ :attribute doit être une date valide.',
    'email' => 'Le champ :attribute doit être une adresse e-mail valide.',
    'unique' => 'Cette :attribute est déjà utilisée.',
    'confirmed' => 'La confirmation du :attribute ne correspond pas.',
    'in' => 'La valeur choisie pour le champ :attribute n’est pas valide.',
    'after_or_equal' => 'Le champ :attribute doit être une date égale ou postérieure au :date.',
    'before_or_equal' => 'Le champ :attribute doit être une date antérieure ou égale au :date.',

    'min' => [
        'numeric' => 'Le champ :attribute doit être supérieur ou égal à :min.',
        'string' => 'Le champ :attribute doit contenir au moins :min caractères.',
    ],

    'max' => [
        'numeric' => 'Le champ :attribute ne doit pas dépasser :max.',
        'string' => 'Le champ :attribute ne doit pas dépasser :max caractères.',
    ],

    // Règles du mot de passe robuste (Password::min(8)->mixedCase()->numbers()->symbols())
    'password' => [
        'letters' => 'Le :attribute doit contenir au moins une lettre.',
        'mixed' => 'Le :attribute doit contenir au moins une majuscule et une minuscule.',
        'numbers' => 'Le :attribute doit contenir au moins un chiffre.',
        'symbols' => 'Le :attribute doit contenir au moins un caractère spécial.',
        'uncompromised' => 'Ce :attribute est apparu dans une fuite de données. Choisissez-en un autre.',
    ],

    // Noms des champs affichés dans les messages
    'attributes' => [
        'name' => 'nom',
        'email' => 'adresse e-mail',
        'password' => 'mot de passe',
        'montant' => 'montant',
        'description' => 'description',
        'dateDepense' => 'date',
        'dateRevenu' => 'date',
        'idCategorie' => 'catégorie',
        'source' => 'source',
        'nomDepenseRecurrente' => 'nom',
        'frequence' => 'fréquence',
        'prochaineDate' => 'prochaine date',
        'titre' => 'titre',
        'message' => 'message',
        'type' => 'type',
        'dateNotification' => 'date',
    ],

];
