<?php

namespace App\Models\app\Bot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bmain extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','area','description','platform','header_key','header_value','user','password','status_active'
    ];

    protected $casts = [
        'status_active' => 'string',
    ];

    const COLUMN_COMMENTS = [
        'name' => 'Nombre',
        'area' => 'Área',
        'description' => 'Descripción',
        'platform' => 'Plataforma de mensajería',
        'header_key' => 'Nombre del header',
        'header_value' => 'Valor del header',
        'user' => 'Usuario API',
        'password' => 'Clave de usuario API',
        'status_active' => 'Estado de activación',
        'finicial' => 'Fecha inicial',
        'ffinal' => 'Fecha Final',
    ];

    public function boptions()
    {
        return $this->hasMany(Boption::class);
    }

    public static function listArea()
    {
        return [
            'DIRECCION'=>'DIRECCION',
            'AUTORIDAD'=>'AUTORIDAD',
            'ADMINISTRACION'=>'ADMINISTRACION',
            'CONTROL ESTUDIO'=>'CONTROL ESTUDIO',
            'PROFESORADO'=>'PROFESORADO',
            'ESTUDIANTIL'=>'ESTUDIANTIL',
            'REPRESENTANTE'=>'REPRESENTANTE',
        ];
    }

    public static function getForArea($area, $is_admin = false)
    {
        $bmains = Bmain::select('bmains.*');
        $bmains = ($is_admin) ? $bmains : $bmains->where('area', $area);
        $bmains = $bmains->get();
        return $bmains;
    }

    public static function listBmains()
    {
        return Bmain::select('name', 'id')->orderBy('name','asc')->pluck('name', 'id');
    }

    public static function getResponseContext()
    {
        return Boption::whereHas('bmain', function ($query) {
            $query->where('status_active', 'true');
        })
        ->select(
            'boptions.*',
            \DB::raw("CONCAT(boptions.description, ' ', boptions.text) as response")
        )
        ->get();
    }

    public static function getResponseContextString()
    {
        return Bmain::getResponseContext()
            ->pluck('response')
            ->implode(' . - . ');
    }
}
