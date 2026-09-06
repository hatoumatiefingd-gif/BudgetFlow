<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model

{

    // Champs autorisés lors de la création d'un message.

    protected $fillable = [

        'name',

        'email',

        'message',

        'lu',

    ];

}
 