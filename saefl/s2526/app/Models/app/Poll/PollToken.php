<?php

namespace App\Models\app\Poll;

use App\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use Illuminate\Support\Str;

class PollToken extends Model
{
    use HasFactory;

    protected $fillable = [
        'poll_main_id','user_id','token','email','status_notifiled'
    ];
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'poll_main_id' => 'Nombre de la Consulta',
        'user_id' => 'Usuario',
        'representant_id' => 'Representante',
        'estudiant_id' => 'Estudiante',
        'profesor_id' => 'Profesor',
        'worker_id' => 'Personal',
        'email' => 'Email',
        'token' => 'Token',
        'status_notifiled' => 'Notificación enviada',
    ];

    public function poll_main()
    {
        return $this->belongsTo(PollMain::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getEmailHideAttribute()
    {
        $str_name = strstr($this->email,"@",true); //dd();
        $str_dominio = strstr($this->email,"@",false); //dd();
        $len_name = strlen($str_name); //dd($str_name,$str_dominio,$len_name);
        $limit =  ($len_name > 5) ? ($len_name-5) : 2;
        $name_hide = Str::limit($str_name,$limit,'*****');
        return $name_hide.$str_dominio;
    }
}

