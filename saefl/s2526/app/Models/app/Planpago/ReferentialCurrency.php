<?php

namespace App\Models\app\Planpago;

use Illuminate\Database\Eloquent\Model;

class ReferentialCurrency extends Model
{
    protected $dates = ['created_at','updated_at'];

    const COLUMN_COMMENTS = [
        'id' => 'Identificador',
        'name'=>'Nombre',
        'symbol'=>'Símbolo',
        'status_cripto'=>'Cripto Moneda',
        'status_forgering'=>'Moneda extranjera',
        'observations'=>'Observaciones'
    ];

    protected $fillable = [ 'name','symbol','status_cripto','observations','status_forgering'];

    public function exchange_rates()
    {
        return $this->hasMany('App\Models\app\Planpago\ExchangeRate');
    }

}
