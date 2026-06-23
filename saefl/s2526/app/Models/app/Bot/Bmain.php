<?php

namespace App\Models\app\Bot;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Bmain extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','area','description','platform','header_key','header_value','user','password','status_active'
    ];
    protected $dates = ['created_at','updated_at'];

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
        return $this->hasMany('App\Models\app\Bot\Boption');
    }

    public static function list_area() /* usada para llenar los objetos de formularios select*/
    {
        return [
            // 'SISTEMA'=>'SISTEMA',
            'DIRECCION'=>'DIRECCION',
            'AUTORIDAD'=>'AUTORIDAD',
            'ADMINISTRACION'=>'ADMINISTRACION',
            'CONTROL ESTUDIO'=>'CONTROL ESTUDIO',
            'PROFESORADO'=>'PROFESORADO',
            'ESTUDIANTIL'=>'ESTUDIANTIL',
            'REPRESENTANTE'=>'REPRESENTANTE',
        ];
    }

    public static function getforArea($area,$is_admin=false)
    {
        $bmains = Bmain::select('bmains.*');
        $bmains = ($is_admin) ? $bmains : $bmains->where('area',$area);
        $bmains = $bmains->get();
        return $bmains;
    }

    public static function list_bmains() /* usada para llenar los objetos de formularios select*/
    {
        $list_bmains = Bmain::select('name', 'id')->orderby('name','asc')->pluck('name', 'id');

        return $list_bmains;
    }

    public static function getResponseContext()
    {
        return Boption::whereHas('bmain', function ($query) {
            $query->where('status_active', 'true');
        })
        ->select(
            'boptions.*',
            DB::raw("CONCAT(boptions.description, ' ', boptions.text) as response")
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


/*

$table->smallIncrements('id');
$table->string('name')->comment('Nombre');
$table->string('description')->comment('Descripción del Bot');
$table->string('platform')->comment('Plataforma de mensajería');
$table->string('header_key')->comment('Nombre del header');
$table->string('header_value')->comment('Valor del header');
$table->string('user')->comment('Ususario API');
$table->string('password')->comment('Clave de usuario API');
$table->enum('status_active',['true','false'])->default('false')->comment('Estado de activación');
$table->date('finicial');
$table->date('ffinal');

*/
