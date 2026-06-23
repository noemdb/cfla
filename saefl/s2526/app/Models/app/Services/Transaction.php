<?php

namespace App\Models\app\Services;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'representant_id','data'
    ];
    const COLUMN_COMMENTS = [
        'representant_id' => 'Representante',
        'data'=>'Response JSON',
        'ammount'=>'Monto',
        'created_at'=>'Fecha',
        'approval'=>'approval',
        'sequence'=>'sequence',
        'order_number'=>'N.Orden',
        'oepration'=>'Operación',
        'bank_acronym'=>'Banco Emisor',
        'json'=>'Data',
    ];

    public function representant()
    {
        return $this->belongsTo('App\Models\app\Estudiante\Representant');
    }

    public function getJsonAttribute()
    {
        return json_decode($this->data);
    }

    public function getAmmountAttribute()
    {
        if (is_object($this->json)) { //dd($this->json);
            if (property_exists($this->json,'amount_formatted')) {
                return $this->json->amount_formatted;
            }
        }
    }

    public function getApprovalAttribute()
    {
        if (is_object($this->json)) {
            if (property_exists($this->json,'credicard')) {
                if (is_object($this->json->credicard)) {
                    if (property_exists($this->json->credicard,'approval')) {
                        return $this->json->credicard->approval;
                    }
                }
            }
        }
    }

    public function getSequenceAttribute()
    {
        if (is_object($this->json)) {
            if (property_exists($this->json,'credicard')) {
                if (is_object($this->json->credicard)) {
                    if (property_exists($this->json->credicard,'sequence')) {
                        return $this->json->credicard->sequence;
                    }
                }
            }
        }
    }

    public function getOrderNumberAttribute()
    {
        if (is_object($this->json)) {
            if (property_exists($this->json,'credicard')) {
                if (is_object($this->json->credicard)) {
                    if (property_exists($this->json->credicard,'order_number')) {
                        return $this->json->credicard->order_number;
                    }
                }
            }
        }
    }

    public function getBankAcronymAttribute()
    {
        if (is_object($this->json)) {
            if (property_exists($this->json,'payment_method')) {
                if (is_object($this->json->payment_method)) {
                    if (property_exists($this->json->payment_method,'payment_card')) {
                        if (is_object($this->json->payment_method->payment_card)) {
                            if (property_exists($this->json->payment_method->payment_card,'bank')) {
                                if (is_object($this->json->payment_method->payment_card->bank)) {
                                    if (property_exists($this->json->payment_method->payment_card->bank,'acronym')) {
                                        $code = $this->json->payment_method->payment_card->bank->code;
                                        $acronym = $this->json->payment_method->payment_card->bank->acronym;
                                        return '('.$code.') '. $acronym;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public function getStatusAttribute()
    {
        if (is_object($this->json)) {
            if (property_exists($this->json,'state')) {
                return ($this->json->state == "APPROVED") ? true : false;
            }
        }
    }
}
