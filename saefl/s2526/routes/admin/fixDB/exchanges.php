<?php

    //cargar tasas de cambios
    Route::get('/fixCAFIngresoCreateING',function (){
        $fix_db = App\Http\Controllers\Admin\FixDB\LoadExchangeRate::fixCAFIngresoCreateING();
    });
    //cargar tasas de cambios
    Route::get('/fixCAFIngresoCreateABN',function (){
        $fix_db = App\Http\Controllers\Admin\FixDB\LoadExchangeRate::fixCAFIngresoCreateABN();
    });
    //cargar tasas de cambios
    Route::get('/fixCAFIngresoCreateCAF',function (){
        $fix_db = App\Http\Controllers\Admin\FixDB\LoadExchangeRate::fixCAFIngresoCreateCAF();
    });

    //cargar tasas de cambios
    Route::get('/load_tdc_bcv_csv',function (){
        $fix_db = App\Http\Controllers\Admin\FixDB\LoadExchangeRate::load_tdc_bcv_csv();
    });

    //fix_date_payment
    Route::get('/fix_date_payment_ingresos',function (){
        $fix_db = App\Http\Controllers\Admin\FixDB\LoadExchangeRate::fix_date_payment_ingresos();
    });
    //fill_date_payment
    // Route::get('/fill_date_payment_ingresos',function (){
    //     $fix_db = App\Http\Controllers\Admin\FixDB\LoadExchangeRate::fill_date_payment_ingresos();
    // });

    //fill exchange rate ingresos
    Route::get('/fill_ingresos_exchange_ammount',function (){
        $fix_db = App\Http\Controllers\Admin\FixDB\LoadExchangeRate::fill_ingresos_exchange_ammount();
    });

    //fill exchange rate cafs
    Route::get('/fill_cafs_exchange_ammount',function (){
        $fix_db = App\Http\Controllers\Admin\FixDB\LoadExchangeRate::fill_cafs_exchange_ammount();
    });

    //fill exchange rate registro pago combinado
    Route::get('/fill_pagos_combinado_exchange_ammount',function (){
        $fix_db = App\Http\Controllers\Admin\FixDB\LoadExchangeRate::fill_pagos_combinado_exchange_ammount();
    });

    //fill exchange rate pagos
    Route::get('/fill_pagos_exchange_ammount',function (){
        $fix_db = App\Http\Controllers\Admin\FixDB\LoadExchangeRate::fill_pagos_exchange_ammount();
    });

    //fill exchange rate registro pago combinado
    Route::get('/fix_creditos_generados_exchange_ammount',function (){
        $fix_db = App\Http\Controllers\Admin\FixDB\LoadExchangeRate::fix_creditos_generados_exchange_ammount();
    });

?>
