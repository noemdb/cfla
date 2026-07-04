<?php

namespace App\Models\app\Estudiante;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Abono extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'representant_id',
        'estudiant_id',
        'ingreso_id',
        'abono_description',
        'status_matriculations',
    ];

    public function ingreso()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Ingreso');
    }

    public function estudiant()
    {
        return $this->belongsTo('App\Models\app\Estudiant');
    }

    public function representant()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Representant');
    }
    public function abono_aplicado()
    {
        return $this->hasOne('App\Models\app\Planpago\AbonoAplicado');
    }

    public function getTotalAbonoAttribute()
    {
        $total = 0;
        foreach ($this as $abono) {
            if (! $abono->status_matriculations ) {
                $total = (!empty($abono->ingreso->ingreso_ammount)) ?  $total + $abono->ingreso->ingreso_ammount : '0';
            }
        }
        return $total;
    }

    public function getAbonoAmmountAttribute()
    {
        return (!empty($this->ingreso)) ? $this->ingreso->ingreso_ammount : null;
    }
    public function getExchangeAmmountAttribute()
    {
        return ($this->exchange) ? $this->exchange->ammount_exchage : null;
    }
    public function getExchangeRateAmmountAttribute()
    {
        return ($this->exchange) ? $this->exchange->ammount_rate : null;
    }

    public function getExchangeRateAttribute()
    {
        $ingreso = Ingreso::withTrashed()->where('id', $this->ingreso_id)->first(); //dd($ingreso);
        return ($ingreso) ? $ingreso->exchange_rate : null;
    }
    public function getExchangeAttribute()
    {
        $ingreso = Ingreso::withTrashed()->where('id', $this->ingreso_id)->first(); //dd($ingreso);
        return ($ingreso) ? $ingreso->exchange : null;
    }

    public function getAllIngresoAttribute()
    {
        $ingreso = Ingreso::withTrashed()->where('id', $this->ingreso_id)->first(); //dd($ingreso);
        return ($ingreso) ? $ingreso : null;
    }
    public function getMetodoPagoAttribute()
    {
        $ingreso = Ingreso::withTrashed()->where('id', $this->ingreso_id)->first(); //dd($ingreso);
        return ($ingreso) ? $ingreso->metodo_pago->name : null;
    }
}
