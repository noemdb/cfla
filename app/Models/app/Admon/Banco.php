<?php

namespace App\Models\app\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banco extends Model
{
    use HasFactory;
    const COLUMN_COMMENTS = [
        'id' => 'Identificador',
        'abbreviation' => 'Abreviación - cinco (5) carácteres',
        'institucion_id' => 'Institución',
        'name' => 'Nombre',
        'description' => 'Descripción interna del banco',
        'logo' => 'Logo del Banco',
        'currency_id'=>'Moneda',
        'number_id_bank' => 'Número de identificación del banco:',
        'number_acount_bank' => 'Número de la cuenta',
        'commission_POS_bank' => 'Porcentaje de TDC',
        'commission_IGTF_bank' => 'Porcentaje de IGTF',
        'status_active_bank' => 'Estado del Banco',
        'is_public' => 'Público',
        'is_intern' => 'Interno',
        'is_adjustment' => 'Banco de Ajuste',
        'is_cash' => 'Fondo Efectivo',
    ];

    protected $fillable = [
        'institucion_id','name','abbreviation','description','logo','currency_id','number_id_bank','number_acount_bank','commission_POS_bank','commission_IGTF_bank','status_active_bank','is_public','is_intern','is_cash'
    ];

    public function ingresos()
    {
        return $this->hasMany('App\Models\app\Estudiante\Ingreso');
    }
    public function abono()
    {
        return $this->hasMany('App\Models\app\Estudiante\Abono');
    }
    public function institucion()
    {
        return $this->belongsTo('App\Models\app\Institucion');
    }

    public static function list_public_bancos() /* usada para llenar los objetos de formularios select*/
    {
        $list_public_bancos = Banco::select('name', 'id')->where('is_public','true')->orderby('name','asc')->pluck('name', 'id');

        return $list_public_bancos;
    }

    public function getStatusForgeringAttribute()
    {
        $currency = ($this->currency) ? $this->currency:false ;

        return ($currency) ? $currency->status_forgering:false ;
    }

    public static function banco_list() /* usada para llenar los objetos de formularios select*/
    {
        $banco_list = Banco::where('status_active_bank','true')->where('is_intern','true')->orderby('name','asc')->pluck('name', 'id');

        return $banco_list;
    }

    public function scopeActive($query, $flag=true)
    {
        return $query->where('bancos.status_active_bank', $flag);
    }
    public function scopePublic($query, $flag=true)
    {
        return $query->where('bancos.is_public', $flag);
    }


}
