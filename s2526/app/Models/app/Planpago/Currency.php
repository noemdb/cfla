<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'id' => 'Identificador',
        'name'=>'Nombre',
        'symbol'=>'Símbolo',
        'status_primary'=>'Moneda Primaria',
        'status_secondary'=>'Moneda Secundaria',
        'status_cripto'=>'Cripto Moneda',
        'status_forgering'=>'Moneda extranjera',
        'observations'=>'Observaciones'
    ];

    protected $fillable = [ 'name','symbol','status_primary','status_secondary','status_cripto','observations','status_forgering'];

    public function referential_currencies()
    {
        return $this->hasMany('App\Models\app\Planpago\ReferentialCurrency');
    }

}
