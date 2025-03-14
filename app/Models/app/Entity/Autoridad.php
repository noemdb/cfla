<?php

namespace App\Models\app\Entity;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Autoridad extends Model
{
    use HasFactory;

    public $table = 'autoridads';

    protected $fillable = [
        'pescolar_id','institucion_id','user_id','tipo_id','name','lastname','ci','city_birth','town_hall_birth','state_birth','country_birth','position','profile_professional','photo','finicial','ffinal',
    ];

    const COLUMN_COMMENTS = ['user_id' => 'Usuario de la Autoridad','tipo_id' => 'Tipo de Autoridad','pescolar_id' => 'Período Escolar',
    'institucion_id' => 'Institución','name' => 'Nombres','lastname' => 'Apellidos','ci' => 'Cédula de Identidad',
    'position' => 'Cargo de la Autoridad','profile_professional' => 'Perfíl Profesional','photo' => 'Imagen',
    'city_birth' => 'Lugar de nacimiento', 'town_hall_birth' => 'Municipio de nacimiento','state_birth' => 'Estado de nacimiento','country_birth' => 'País de nacimiento',
    'finicial' => 'Fecha Inicial','ffinal' => 'Fecha Final'];

    public function pescolar()
    {
        return $this->belongsTo(Pescolar::class,'pescolar_id');
    }
    public function institucion()
    {
        return $this->belongsTo(Institucion::class, 'institucion_id');
    }
    // public function tautoridad()
    // {
    //     return $this->belongsTo(Tautoridad::class,'tipo_id');
    // }
    public function user()
    {
        return $this->belongsTo(User::class,'user_id');
    }

    public function getFullNameAttribute()
    {
        return "{$this->name} {$this->lastname}";
    }

    public function getFullName2Attribute()
    {
        return "{$this->name} {$this->lastname}";
    }

    public static function getTipoAuthority($tipo_id)
    {
        $autoridad = Autoridad::OrderBy('created_at','DESC')
            ->Where('tipo_id', $tipo_id)
            ->Where('finicial', '<=', Carbon::now())
            ->Where('ffinal', '>=', Carbon::now())
            ->first();
        return ($autoridad) ? $autoridad : '';
    }

    public static function getAuthority($position)
    {
        $autoridad = Autoridad::OrderBy('created_at','DESC')
            ->Where('finicial', '<=', Carbon::now())
            ->Where('ffinal', '>=', Carbon::now())
            ->Where('position', $position)
            ->first();
        return ($autoridad) ? $autoridad : '';
    }

    public static function list_autoridads() /* usada para llenar los objetos de formularios select*/
    {
        return
            Autoridad::select('autoridads.*')
            ->selectRaw("CONCAT(autoridads.name,' ', autoridads.lastname ,' [',autoridads.position,']') as autoridad_fullname")
            ->where('autoridads.id','<>',2) //Corresponde al director general
            ->orderby('name','asc')
            ->pluck('autoridad_fullname', 'id');
    }

}
