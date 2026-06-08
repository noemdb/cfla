<?php

namespace App\Models\app\Institucion;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

use Illuminate\Support\Carbon;
use Jenssegers\Date\Date;
use Illuminate\Support\Facades\DB;

use App\Models\app\Institucion;
// use App\Models\app\Institucion\Autoridad;
use App\Models\app\Pescolar\Pestudio;
use Illuminate\Support\Str;

class Autoridad extends Model
{

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'pescolar_id','institucion_id','user_id','tipo_id','name','lastname','ci','city_birth','town_hall_birth','state_birth','country_birth','position','profile_professional','mail_cc_address','photo','finicial','ffinal',
    ];

    const COLUMN_COMMENTS = ['user_id' => 'Usuario de la Autoridad','tipo_id' => 'Tipo de Autoridad','pescolar_id' => 'Período Escolar',
    'institucion_id' => 'Institución','name' => 'Nombres','lastname' => 'Apellidos','ci' => 'Cédula de Identidad',
    'position' => 'Cargo de la Autoridad','profile_professional' => 'Perfíl Profesional','mail_cc_address'=>'Dirección de Correo CC ECA','photo' => 'Imagen',
    'city_birth' => 'Lugar de nacimiento', 'town_hall_birth' => 'Municipio de nacimiento','state_birth' => 'Estado de nacimiento','country_birth' => 'País de nacimiento',
    'finicial' => 'Fecha Inicial','ffinal' => 'Fecha Final'];

    public function pescolar()
    {
        return $this->belongsTo('App\Models\app\Pescolar');
    }
    public function institucion()
    {
        return $this->belongsTo('App\Models\app\Institucion');
    }
    public function tautoridad()
    {
        return $this->belongsTo('App\Models\app\Institucion\Tautoridad','tipo_id');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
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

    public function getCiFullF2(string $separator = ' '): string
    {
        $nationality = $this->country_birth === 'VENEZUELA' ? 'V' : 'E';
        return sprintf('%s%s%s', $nationality, $separator, $this->formatted_ci_custom);
    }

    public function getFormattedCiCustom2Attribute(): string
    {
        // 1. Eliminar cualquier punto (y opcionalmente otros caracteres no numéricos)
        $cleanCi = str_replace('.', '', $this->ci);
        
        // 2. Eliminar solo los ceros a la izquierda que sean extra (pero dejar al menos un cero si todo son ceros)
        //    Esto no se hace con ltrim porque perderíamos la longitud correcta.
        //    En su lugar, forzamos a 8 dígitos con ceros a la izquierda.
        $padded = str_pad($cleanCi, 8, '0', STR_PAD_LEFT);
        
        // 3. Tomar los últimos 8 dígitos (por si el string original tenía más de 8)
        $padded = substr($padded, -8);
        
        // 4. Formatear como XX.XXX.XXX
        return substr($padded, 0, 2) . '.' . substr($padded, 2, 3) . '.' . substr($padded, 5, 3);
    }


public function getFormattedCiCustomAttribute(): string
{
    // 1. Solo dígitos, incluso si el valor original es null
    $cleanCi = preg_replace('/\D/', '', $this->ci ?? '');

    // 2. Si tiene menos de 7 dígitos, rellenar con ceros a la izquierda
    if (strlen($cleanCi) < 7) {
        $cleanCi = Str::padLeft($cleanCi, 7, '0');
    }

    // 3. Formatear con puntos si la longitud es 7 u 8; si no, devolver sin puntos
    return preg_replace('/^(\d{1,2})(\d{3})(\d{3})$/', '$1.$2.$3', $cleanCi);
}

}
