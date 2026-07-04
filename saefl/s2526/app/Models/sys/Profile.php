<?php

namespace App\Models\sys;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Profile extends Model
{
	use Notifiable;

    protected $fillable = [
        'user_id','card_number','firstname', 'lastname','url_img','email'
    ];

    const COLUMN_COMMENTS = [
        'card_number' => 'CI de Identidad',
        'firstname' => 'Nombres',
        'lastname' => 'Apellidos',
    ];

    /*INI relaciones entre modelos*/
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    /*FIN relaciones entre modelos*/

    public function getFullNameAttribute()
    {
        return $this->firstname .' ' . $this->lastname;
    }

    public function getFullNameInvAttribute()
    {
        return $this->lastname . ' ' . $this->firstname  ;
    }
    public function getCountAttribute()
    {
    // return $this->firstname .' ' .$this->lastname;
        return $this->count();
    }
}
