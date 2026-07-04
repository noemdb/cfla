<?php

namespace App\Models\sys;

use Illuminate\Database\Eloquent\Model;
// Helpers
use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

// Modelos
use App\User;


class Task extends Model
{
	//usada para el softdelete
 	protected $dates = ['deleted_at'];

  protected $fillable = [
        'user_id','codigo', 'descripcion','tipo', 'evento','estado','finicial','ffinal'
    ];

  // Para obtener los getAttribute
	// protected $appends = ['userlist'];

	/*INI relaciones entre modelos*/
	public function user()
  {
    return $this->belongsTo('App\User');
  }
  /*FIN relaciones entre modelos*/

   // public function getUserListAttribute()
   //  {
   //    return User::orderBy('username','desc')->has('tasks')->pluck('username', 'id');
   //    // return $this->firstname .' ' . $this->lastname;
   //  }

  public static function getUserTasks($finicial,$ffinal,$limit=10)
  {
    $userstasks = Task::select('users.username','tasks.user_id',DB::raw('count(tasks.id) as tasks_count'))
      ->join('users', 'users.id', '=', 'tasks.user_id')
      ->Where('tasks.created_at', '>=', $finicial)
      ->Where('tasks.created_at', '<=', $ffinal)
      ->groupby('users.username')
      ->orderBy('tasks_count', 'desc')
      ->get()
      ->take($limit);

    return ($userstasks) ? $userstasks : 0;
  }

  public static function getCountTotal($arr_user_id,$finicial,$ffinal, $estado)
  {
    //INI array con los totales de las tasks
    foreach ($arr_user_id as $key => $value) {
      $tasks =
        Task::where('created_at', '>=', $finicial)
          ->where('created_at', '<=', $ffinal)
          ->where('estado', 'like', '%'.$estado.'%')
          ->where('user_id',$value)
          ->groupBy('user_id')
          ->get([
              DB::raw('COUNT(*) as value')
          ]);
      if( $tasks->count()>0){
          $arr_total[] = $tasks->first()->value;
      }
    }
    //FIN array con los totales de las tasks

    return (isset($arr_total)) ? $arr_total : 0;
  }

  public function getTruncDescripcionAttribute()
  {
    $string = $this->descripcion;
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
