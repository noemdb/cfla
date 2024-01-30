<?php

namespace App\Models\app\Admon;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExchangeRate extends Model
{
    use HasFactory;

    const COLUMN_COMMENTS = [
        'id' => 'Identificador',
        'currency_id' => 'Moneda',
        'currency_referential_id' => 'Moneda Referencial',
        'date'=>'Fecha',
        'ammount'=>'Monto Venta',
        'ammount_buy'=>'Monto Compra',
        'source'=>'Fuente de la información',
        'name'=>'Nombre',
        'observations'=>'Observaciones',
        'image'=>'Imagen Informativa',
        'status_official'=>'Oficial',
        'user_id'=>'Usuario'
    ];

    protected $fillable = [
        'currency_id',
        'currency_referential_id',
        'date',
        'ammount',
        'ammount_buy',
        'source',
        'name',
        'observations',
        'image',
        'status_official',
        'user_id'
    ];

    protected $dates = ['created_at','updated_at','date'];

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\app\Planpago\Currency');
    }

    public function currency_referential()
    {
        return $this->belongsTo('App\Models\app\Planpago\ReferentialCurrency');
    }

}
