<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;



class SyncData extends Model
{
    protected $fillable = ['user_id','rol_id'];

    protected $date = ['created_at','updated_at'];

	/*INI relaciones entre modelos*/
        public function user()
        {
            return $this->belongsTo('App\User');
        }
        public function rol()
        {
            return $this->belongsTo('App\sys\Rol');
        }
    /*FIN relaciones entre modelos*/
}
