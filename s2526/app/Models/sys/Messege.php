<?php

namespace App\Models\sys;

use Illuminate\Database\Eloquent\Model;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

// Modelos
use App\User;

class Messege extends Model
{
	/*INI relaciones entre modelos*/
	public function user()
    {
        return $this->belongsTo('App\User');
    }
    /*FIN relaciones entre modelos*/

    protected $fillable = [
	    'user_id','destino_user_id', 'mensaje','tipo','estado'
	  ];

  public function getTruncMensajeAttribute()
  {
    $string = $this->mensaje;
    $length = 4;
    $ellipsis = "...";
    $words = explode(' ', $string);
    if (count($words) > $length){
        return implode(' ', array_slice($words, 0, $length)) ." ". $ellipsis;
    }
    else{
        return $string;
    }
  }

  public static function getUserMesseges($finicial,$ffinal,$limit=10)
  {
    $usersalerts = Messege::select('users.username','messeges.user_id',DB::raw('count(messeges.id) as messeges_count'))
      ->join('users', 'users.id', '=', 'messeges.user_id')
      ->Where('messeges.created_at', '>=', $finicial)
      ->Where('messeges.created_at', '<=', $ffinal)
      ->groupby('users.username')
      ->orderBy('messeges_count', 'desc')
      ->get()
      ->take($limit);

    return ($usersalerts) ? $usersalerts : 0;
  }

  public static function getCountTotal($arr_user_id,$finicial,$ffinal, $estado=NULL)
  {
    //INI array con los totales de las messeges
    foreach ($arr_user_id as $key => $value) {
      $messeges =
        Messege::where('created_at', '>=', $finicial)
          ->where('created_at', '<=', $ffinal)
          ->where('estado', 'like', '%'.$estado.'%')
          ->where('user_id',$value)
          ->groupBy('user_id')
            ->get([
              DB::raw('COUNT(*) as value')
          ]);

      if( $messeges->count()>0){
          $arr_total[] = $messeges->first()->value;
      }
    }
    //FIN array con los totales de las messeges

    return (isset($arr_total)) ? $arr_total : 0;
  }

  public function getClassAttribute()
  {      
    switch ($this->tipo) {
      case 'Conversación':
          $class = 'primary'; break;
      case 'Información':
          $class = 'info'; break;
      case 'Solicitud':
          $class = 'success'; break;
      case 'Alerta':
          $class = 'warning'; break;          
      default:
          $class = 'secondary'; break;
    }
    return $class;
  }
}

