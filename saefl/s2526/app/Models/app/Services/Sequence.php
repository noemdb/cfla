<?php

namespace App\Models\app\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sequence extends Model
{
    use HasFactory;

    protected $fillable = [
        'token','ci_representant','step','card_number','type_ci','cvc','expiration_month','expiration_year','date_expiration','account_type','card_pin','card_type','card_type_holder','holder_name','holder_id_doc','holder_id','access_token','date_expires','token_bank','ammount_pay','ammount_pay_exchange','exchange_ammount',
    ];
    public function representant()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Representant','ci_representant');
    }
}
