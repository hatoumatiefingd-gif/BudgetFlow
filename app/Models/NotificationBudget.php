<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationBudget extends Model
{
    protected $table = 'notification';
    protected $primaryKey = 'idNotification';
    public $timestamps = false;

    protected $fillable = [
        'titre',
        'message',
        'type',
        'dateNotification',
        'idUtilisateur',
    ];
}