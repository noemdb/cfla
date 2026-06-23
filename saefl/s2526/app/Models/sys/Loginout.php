<?php

namespace App\Models\sys;

use Illuminate\Database\Eloquent\Model;

// añadidos
use Request;

class Loginout extends Model
{
	/*INI relaciones entre modelos*/
	public function user()
    {
        return $this->belongsTo('App\User');
    }
    /*FIN relaciones entre modelos*/

    protected $fillable = [
        'action', 'tipo', 'message', 'context', 'extra','ip', 'view', 'user_id'
    ];

    public static function saveLogin($user_id,$username){
        $data = array(
            'user_id' => $user_id, 
            'message' => 'El usuario '.$username.' inició sesión correctamente',
            'action' => 'LogSuccessfulLogin',
            'tipo' => 'success',
            'ip' => Request::ip(),
        );
        $Loginout = Loginout::create($data);
    }

    public static function saveLoginOut($user_id,$username){
        $data = array(
            'user_id' => $user_id, 
            'message' => 'El usuario '.$username.' cerró sesión correctamente',
            'action' => 'LogSuccessfulLogout',
            'tipo' => 'danger',
            'ip' => Request::ip(),
        );
        $Loginout = Loginout::create($data);
    }
}
