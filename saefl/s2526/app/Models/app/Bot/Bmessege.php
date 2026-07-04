<?php

namespace App\Models\app\Bot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bmessege extends Model
{
    use HasFactory;

    protected $fillable = [
        'bmain_id','app_package_name','messenger_package_name','sender','message','is_group','rule_id',
        'ip_sender','header_key','header_value',
        'host','user_agent','accept_encoding','connection',
    ];
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'bmain_id' => 'Autorrespomdedor',
        'app_package_name' => 'Servicio BOT',
        'messenger_package_name' => 'Servidor de Mensajería',
        'sender' => 'Enviador',
        'message' => 'Mensaje',
        'is_group' => 'Grupo',
        'rule_id' => 'Nùmero de la regla',
        'ip_sender' => 'IP Enviador',
        'header_key' => 'header key',
        'header_value' => 'header value',
        'host' => 'Host',
        'user_agent' => 'User-Agent',
        'accept_encoding' => 'Accept-Encoding',
        'connection' => 'Connection',
        'created_at' => 'Fecha'
    ];

    public function bmain()
    {
        return $this->belongsTo('App\Models\app\Bot\Bmain');
    }


    public static function getforArea($area,$is_admin=false)
    {
        $bmesseges = Bmessege::select('bmesseges.*')->leftjoin('bmains', 'bmains.id', '=', 'bmesseges.bmain_id')->orderBy('bmesseges.created_at','desc');
        $bmesseges = ($is_admin) ? $bmesseges : $bmesseges->where('bmains.area',$area);
        $bmesseges = $bmesseges->get();
        return $bmesseges;
    }

    public function getBmainNameAttribute()
    {
        return ($this->bmain) ? $this->bmain->name : null;
    }

    public function getBmainAreaAttribute()
    {
        return ($this->bmain) ? $this->bmain->area : null;
    }
    public function getBmainDescriptionAttribute()
    {
        return ($this->bmain) ? $this->bmain->description : null;
    }

    public function getFullNameAttribute()
    {
        return "{$this->BmainName} - {$this->BmainArea}";
    }
}


/*

$table->string('app_package_name')->comment('Nombre de la aplicación');
$table->string('messenger_package_name')->comment('Nombre del servidor de mensajería');
$table->string('sender')->comment('Enviador');
$table->string('message')->comment('Mensaje');
$table->string('ip_sender')->nullable()->comment('Mensaje');
$table->string('header_key')->nullable()->comment('header key');
$table->string('header_value')->nullable()->comment('header value');
$table->string('host')->nullable()->comment('host');
$table->string('user_agent')->nullable()->comment('User-Agent');
$table->string('accept_encoding')->nullable()->comment('Accept-Encoding');
$table->string('connection')->nullable()->comment('Connection');

*/
