<?php

namespace App\Models\sys;

use Illuminate\Database\Eloquent\Model;

class Logdb extends Model
{
	/*INI relaciones entre modelos*/
	public function user()
    {
        return $this->belongsTo('App\User');
    }
    /*FIN relaciones entre modelos*/


    public static function array_request($array_request = null){
        $str = '';

    	foreach ($array_request as $key => $value) {
    		if ($key <> 'password' && $key <> '_token') {
                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        if (is_array($v)) {
                            foreach ($v as $kk => $vv) {
                                if (is_array($vv)){
                                    // foreach ($vv as $kkk => $vvv) {
                                    //     $str .= $kkk.'=>'.$vvv.', ';
                                    // }
                                } else {
                                    $str .= $kk.'=>'.$vv.', ';
                                }
                            }
                        } else {
                            $str .= $k.'=>'.$v.', ';
                        }
                    }
                } else {
                    $str .= $key.'=>'.$value.', ';
                }
    		}
    	}
    	return substr($str,0,-2);
    }

    public static function record($action, $class = null){
    	$logdb = new Logdb();
        $request = request();
        $pathInfo = $request->getPathInfo();
        $pos_login = strpos($pathInfo, '/login');
        $pos_logout = strpos($pathInfo, '/logout');
        if($pos_login === false && $pos_logout === false){ //excluyendo los registros de login o logout
            $arr_request = $request->ToArray();
            $logdb->data = $logdb->array_request($arr_request);
        	$logdb->action = $action;
            $logdb->tipo = $logdb->gettype($action);
            $logdb->user_id = auth()->user()->id;
            $logdb->ip = $request->ip();
            $logdb->pathInfo = $pathInfo;
            $logdb->url =$request->server('HTTP_REFERER');
            $logdb->model_class = $class;

            // dd($logdb);

            $logdb->save();
        }
    }
    public static function gettype($action){
        switch ($action) {
            case 'created': $tipo = 'primary'; break;
            case 'updated': $tipo = 'info'; break;
            case 'deleted': $tipo = 'danger'; break;
            case 'restored': $tipo = 'warning'; break;
            default: $tipo = 'default'; break;
        }
        return $tipo;
    }
}
