<?php

namespace App\Models\app;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Institucion extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id','name','rif_institution','email_institution',
        'code','code_oficial','code_private','password','format_bill','legalname',
        'phone','phone2','phone3','address','town_hall','city','state','state_code','country',
        'number_fill_contingency','status_enabled_number_a_bill','last_number_bill_config',
        'status_print_bill_economical','status_dont_allow_registration_if_insolvency',
        'status_no_show_info_academic','status_proof_of_payment','status_credit_bills',
        'status_printer_fiscal','status_print_number_bill','status_skip_discount',
        'status_enabled_inscription_academic','concept_islr','status_apply_tax','percent_tax',
        'provider_payonline','bank_payonline','percent_comission_payonline','percent_POSVirtual',
        'status_exchange_rate','ammount_exchange_rate','observation_default_bill','observation_default_billing_notice','txt_contract_study','date_suspend',
        'facebook_media_id_control','facebook_media_id_admon',
        //'facebook_tier','facebook_access_token',facebook_access_token_date
    ];

    public function pescolar()
    {
        return $this->hasOne('App\Models\app\Pescolar');
    }

    public function autoridads()
    {
        return $this->hasMany('App\Models\app\Institucion\Autoridad');
    }

    public function bancos()
    {
        return $this->hasMany('App\Models\app\Institucion\Banco');
    }

    public function registro_titulos()
    {
        return $this->hasMany('App\Models\app\RegistroTitulo');
    }


}
