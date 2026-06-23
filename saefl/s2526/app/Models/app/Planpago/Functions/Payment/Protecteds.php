<?php
namespace App\Models\app\Planpago\Functions\Payment;

trait Protecteds {



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
        'banco_id_1',
        'method_pay_id_1',
        'number_i_pay_1',
        'date_transaction_1',
        'ammount_1',
        'observation_1',
        'image_1',

        'banco_emisor_2',
        'banco_id_2',
        'method_pay_id_2',
        'number_i_pay_2',
        'date_transaction_2',
        'ammount_2',
        'observation_2',
        'image_2',

        'banco_emisor_3',
        'banco_id_3',
        'method_pay_id_3',
        'number_i_pay_3',
        'date_transaction_3',
        'ammount_3',
        'observation_3',
        'image_3',

        'banco_emisor_4',
        'banco_id_4',
        'method_pay_id_4',
        'number_i_pay_4',
        'date_transaction_4',
        'ammount_4',
        'observation_4',
        'image_4',
    ];

    /**
    * The database connection used by the model.
    *
    * @var string
    */
    // protected $connection = 'blogs';

}
