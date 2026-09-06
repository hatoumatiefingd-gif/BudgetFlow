<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class NotificationAdmin extends Model
{
   protected $table = 'notification_admin';
   protected $primaryKey = 'idNotification';
   public $timestamps = false;
   protected $fillable = [
       'titre',
       'message',
       'type',
       'dateNotification',
   ];
}