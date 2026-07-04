<?php

namespace App\Models\app\Admon;

use Carbon\Carbon;
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

    protected $casts = [
        'date' => 'date',
    ];

    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function currency()
    {
        return $this->belongsTo('App\Models\app\Admon\Currency');
    }

    public function currency_referential()
    {
        return $this->belongsTo('App\Models\app\Admon\ReferentialCurrency');
    }

    public static function getAmmountExchange()
    {
        $exchange = ExchangeRate::whereDate('date', Carbon::now())
            ->whereNotNull('ammount')
            ->first();

        return ($exchange) ? $exchange->ammount : null;
    }

    public static function getAmmountExchangeNear($date = null)
    {
        $date = $date ?: Carbon::now();
        $exchange = ExchangeRate::whereDate('date', '<=', $date)
            ->whereNotNull('ammount')
            ->orderBy('date', 'desc')
            ->first();

        return ($exchange) ? $exchange->ammount : null;
    }
}
