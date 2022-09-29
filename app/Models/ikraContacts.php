<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;


class ikraContacts extends Model
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'ikra_id',
        'name',
        'email',
        'phone',
        'location',
    ];

    public function ikraUsers()
    {
        return $this->belongsTo(ikraUsers::class,'ikra_id');
    }

    public function routeNotificationForSmsApi() {
        return $this->phone; //Name of the field to be used as mobile
    } 
}
