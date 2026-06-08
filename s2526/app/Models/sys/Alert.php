<?php

namespace App\Models\sys;

use Illuminate\Database\Eloquent\Model;

// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

// Modelos
use App\User;

class Alert extends Model
{
	/*INI relaciones entre modelos*/
	public function user()
    {
        return $this->belongsTo('App\User');
    }
    /*FIN relaciones entre modelos*/

	protected $fillable = [
	    'user_id','destino_user_id', 'mensaje','tipo','estado','finicial','ffinal'
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

  public static function getUserAlerts($finicial,$ffinal,$limit=10)
  {
    $usersalerts = Alert::select('users.username','alerts.user_id',DB::raw('count(alerts.id) as alerts_count'))
      ->join('users', 'users.id', '=', 'alerts.user_id')
      ->Where('alerts.created_at', '>=', $finicial)
      ->Where('alerts.created_at', '<=', $ffinal)
      ->groupby('users.username')
      ->orderBy('alerts_count', 'desc')
      ->get()
      ->take($limit);

    return ($usersalerts) ? $usersalerts : 0;
  }

  public static function getCountTotal($arr_user_id,$finicial,$ffinal, $estado=NULL)
  {
    //INI array con los totales de las alerts
    foreach ($arr_user_id as $key => $value) {
      $alerts =
        Alert::where('created_at', '>=', $finicial)
          ->where('created_at', '<=', $ffinal)
          ->where('estado', 'like', '%'.$estado.'%')
          ->where('user_id',$value)
          ->groupBy('user_id')
            ->get([
              DB::raw('COUNT(*) as value')
          ]);

      if( $alerts->count()>0){
          $arr_total[] = $alerts->first()->value;
      }
    }
    //FIN array con los totales de las alerts

    return (isset($arr_total)) ? $arr_total : 0;
  }

  public function getClassAttribute()
  {      
    switch ($this->estado) {
      case 'Regular':
          $class = 'primary'; break;
      case 'Suspendido':
          $class = 'danger'; break;
      case 'Preinscrito':
          $class = 'info'; break;
      case 'Egresado':
          $class = 'success'; break;          
      default:
          $class = 'secondary'; break;
    }
    return $class;
  }

}
