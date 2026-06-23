<?php

namespace App\Models\app\Bot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bmessege extends Model
{
    use HasFactory;

    protected $table = 'bmesseges';

    protected $fillable = [
        'bmain_id','app_package_name','messenger_package_name','sender','message','is_group','rule_id',
        'ip_sender','header_key','header_value',
        'host','user_agent','accept_encoding','connection',
    ];

    const COLUMN_COMMENTS = [
        'bmain_id' => 'Autorrespomdedor',
        'app_package_name' => 'Servicio BOT',
        'messenger_package_name' => 'Servidor de Mensajería',
        'sender' => 'Enviador',
        'message' => 'Mensaje',
        'is_group' => 'Grupo',
        'rule_id' => 'Número de la regla',
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
        return $this->belongsTo(Bmain::class);
    }

    public static function getForArea($area, $is_admin = false)
    {
        $bmesseges = Bmessege::select('bmesseges.*')
            ->leftJoin('bmains', 'bmains.id', '=', 'bmesseges.bmain_id')
            ->orderBy('bmesseges.created_at', 'desc');
        $bmesseges = ($is_admin) ? $bmesseges : $bmesseges->where('bmains.area', $area);
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
        return "{$this->bmainName} - {$this->bmainArea}";
    }
}
