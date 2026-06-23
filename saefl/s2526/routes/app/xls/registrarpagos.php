<?php
/* XLS */

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
Route::group(['middleware'=>['is_admon']], function(){
    Route::get('/registrarpagos/list/pagos/excel', 'EXCEL\RegistroPagoController@list_pagos_dw_excel')->name('administracion.registrarpagos.list.pagos.dw.excel');

    /*filtros mejorados*/
    Route::get('/registrarpagos/export/list', 'EXCEL\RegistroPagoController@export_list')->name('administracion.registropagos.export.xls');


    Route::get('/registrarpagos/adelantados/export/list', 'EXCEL\PagoAdelantadoController@export_list')->name('administracion.registropagos.adelantados.export.xls');

    Route::get('/registrarpagos/export/representants/cuentaxpagars/pendientes', 'EXCEL\CuentaXPagarController@representants_cuentaxpagars_pendeintes')->name('administracion.registropagos.export.representants.cuentaxpagars.pendientes');
});
?>
