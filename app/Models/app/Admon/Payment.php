<?php

namespace App\Models\app\Admon;

use App\Models\app\Learner\Representant;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $dates = ['created_at','updated_at'];



    protected $fillable = [

        'ci_representant',
        'name_representant',
        'phone',
        'type_pay',
        'comment',
        'payment_id',

        'ci_estudiant_1',
        'name_estudiant_1',
        'grado_estudiant_1',

        'ci_estudiant_2',
        'name_estudiant_2',
        'grado_estudiant_2',

        'ci_estudiant_3',
        'name_estudiant_3',
        'grado_estudiant_3',

        'ci_estudiant_4',
        'name_estudiant_4',
        'grado_estudiant_4',

        'banco_emisor_1',
        'phone_1',
        'banco_id_1',
        'method_pay_id_1',
        'number_i_pay_1',
        'date_transaction_1',
        'ammount_1',
        'observation_1',
        'image_1',

        'banco_emisor_2',
        'phone_2',
        'banco_id_2',
        'method_pay_id_2',
        'number_i_pay_2',
        'date_transaction_2',
        'ammount_2',
        'observation_2',
        'image_2',

        'banco_emisor_3',
        'phone_3',
        'banco_id_3',
        'method_pay_id_3',
        'number_i_pay_3',
        'date_transaction_3',
        'ammount_3',
        'observation_3',
        'image_3',

        'banco_emisor_4',
        'phone_4',
        'banco_id_4',
        'method_pay_id_4',
        'number_i_pay_4',
        'date_transaction_4',
        'ammount_4',
        'observation_4',
        'image_4',
    ];

    /********************************************************** */
    // public function getRepresentantAttribute()
    // {
    //     $representant = Representant::where('ci_representant',$this->ci_representant)->firts();
    //     return $representant;
    // }
    /********************************************************** */

    public function getBanco($id)
    {
        return ($id) ? Banco::findOrFail($id) : null;
    }
    public function getMethod($id)
    {
        return ($id) ? MetodoPago::findOrFail($id) : null;
    }

    public function getTotalAmmountAttribute()
    {
        $ammount = $this->ammount_1 + $this->ammount_2 + $this->ammount_3 + $this->ammount_4 ;
        return $ammount;
    }

    public function getImagesUrlAttribute()
    {
        $images = (object) [
            'url1' => $this->image_1,
            'url2' => $this->image_2,
            'url3' => $this->image_3,
            'url4' => $this->image_4
        ]; //dd($this,$images);
        return $images;
    }

    public function getBancosAttribute()
    {
        $banco_1 = Banco::find($this->banco_id_1); $banco_name_1 = ($banco_1) ? $banco_1->name : null ;
        $banco_2 = Banco::find($this->banco_id_2); $banco_name_2 = ($banco_2) ? $banco_2->name : null ;
        $banco_3 = Banco::find($this->banco_id_3); $banco_name_3 = ($banco_3) ? $banco_3->name : null ;
        $banco_4 = Banco::find($this->banco_id_4); $banco_name_4 = ($banco_4) ? $banco_4->name : null ;
        $bancos = (object) [
            'banco_name_1' => $banco_name_1,
            'banco_name_2' => $banco_name_2,
            'banco_name_3' => $banco_name_3,
            'banco_name_4' => $banco_name_4
        ]; //dd($this,$images);
        return $bancos;
    }

    public function getBancoAttribute()
    {
        return Banco::find($this->banco_id_1);
    }

    public function getNumberIPaysAttribute()
    {
        $number_i_pays = (object) [
            'number_i_pay_1' => $this->number_i_pay_1,
            'number_i_pay_2' => $this->number_i_pay_2,
            'number_i_pay_3' => $this->number_i_pay_3,
            'number_i_pay_4' => $this->number_i_pay_4,
        ]; //dd($this,$images);
        return $number_i_pays;
    }
    public function getAmmountsAttribute()
    {
        $ammounts = (object) [
            'ammount_1' => $this->ammount_1,
            'ammount_2' => $this->ammount_2,
            'ammount_3' => $this->ammount_3,
            'ammount_4' => $this->ammount_4,
        ]; //dd($this,$images);
        return $ammounts;
    }

    public function getMethodsAttribute()
    {
        $method_1 = MetodoPago::find($this->method_pay_id_1); $method_name_1 = ($method_1) ? $method_1->name : null ;
        $method_2 = MetodoPago::find($this->method_pay_id_2); $method_name_2 = ($method_2) ? $method_2->name : null ;
        $method_3 = MetodoPago::find($this->method_pay_id_3); $method_name_3 = ($method_3) ? $method_3->name : null ;
        $method_4 = MetodoPago::find($this->method_pay_id_4); $method_name_4 = ($method_4) ? $method_4->name : null ;
        $bancos = (object) [
            'method_name_1' => $method_name_1,
            'method_name_2' => $method_name_2,
            'method_name_3' => $method_name_3,
            'method_name_4' => $method_name_4
        ]; //dd($this,$images);
        return $bancos;
    }

    public function getRepresentantAttribute()
    {
        $representant = Representant::where('ci_representant',$this->ci_representant)->first();
        return $representant;
    }


    public function getStatusApplyT1Attribute ()
    {
        $ingreso = Ingreso::where('number_i_pay', $this->number_i_pay_1)->first(); //dd($this->number_i_pay_1,$ingreso);
        return ($ingreso) ? true : false ;
    }
    public function getStatusApplyT2Attribute ()
    {
        $ingreso = Ingreso::where('number_i_pay', $this->number_i_pay_2)->first();
        return ($ingreso) ? true : false ;
    }

    public function getRepresentantIdAttribute()
    {
        return ($this->representant) ? $this->representant->id : null;
    }

    const COLUMN_COMMENTS = [
        'representant_id'=>'Representante',
        'ci_representant'=>'CI del representante',
        'type_pay'=>'Motivo',
        'method_pay_id'=>'Método',
        'number_i_pay'=>'Núm. de referencia',
        'phone'=>'Teléfono',
        'date_transaction'=>'Fecha de la transferencia',
        'ammount'=>'Monto',
        'observations'=>'Observaciones',
        'comment'=>'Comentarios',
        'status_approved'=>'Aprobación',
        'status_apply'=>'Aplicación',
        'banco_emisor_1'=>'Banco emisor',
        'phone_1'=>'Teléf. emisor del Pago Móvil',
        'banco_id_1'=>'Banco receptor',
        'method_pay_id_1'=>'Método de Pago',
        'number_i_pay_1'=>'Núm. de referencia',
        'date_transaction_1'=>'Fecha de la transacción',
        'ammount_1'=>'Monto',
        'observation_1'=>'',
        'image'=>'Imagen',
        'image_1'=>'Imagen',
    ];

    const LIST_TYPE_PAY = [
        'Inscripción Período Escolar 2025-2026'=>'Inscripción Período Escolar 2025-2026',
        'Deuda pendiente del Período Escolar 2024 - 2025'=>'Deuda pendiente del Período Escolar 2024 - 2025',
        // 'Inscripción Período Escolar 2020'=>'Inscripción Período Escolar 2020 - 2021',
        'Mensualidad(es) anterior(es)'=>'Mensualidad(es) anterior(es)',
        'Mensualidad actual'=>'Mensualidad actual',
        'Pago(s) por adelantado'=>'Pago(s) por adelantado',
        'Otros'=>'Otros'
    ];

    const LIST_BANK_EMISOR = [
        'BANCO DE VENEZUELA'=>'BANCO DE VENEZUELA',
        'BANCO MERCANTIL'=>'BANCO MERCANTIL',
        'BANCO EXTERIOR'=>'BANCO EXTERIOR',
        'BANCO BANCARIBE'=>'BANCO BANCARIBE',
        'BANCO DEL TESORO'=>'BANCO DEL TESORO',
        'BANCO PROVINCIAL'=>'BANCO PROVINCIAL',
        'BANCO BANESCO'=>'BANCO BANESCO',
        'BANCO BFC'=>'BANCO BFC',
        'BANCO BICENTENARIO'=>'BANCO BICENTENARIO',
        'BANCO SOFITASA'=>'BANCO SOFITASA',
        'BANCO BANFANB'=>'BANCO BANFANB',
        'BANCO CARONÍ'=>'BANCO CARONÍ',
        'BANCO INTERNACIONAL'=>'BANCO INTERNACIONAL',
        'OTRO'=>'OTRO',
    ];


}
