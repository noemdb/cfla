<?php
/* XLS */

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

Route::group(['middleware'=>['is_admon']], function(){
    Route::get('/estudiants/list/saldos/excel', 'EXCEL\EstudiantController@list_saldo_dw_excel')->name('administracion.estudiants.list.saldos.dw.excel');
    Route::get('/estudiants/list/saldos/csv', 'EXCEL\EstudiantController@list_saldo_dw_csv')->name('administracion.estudiants.list.saldos.dw.csv');
});

Route::group(['middleware'=>['is_common']], function(){
    Route::get('/estudiants/list/full/excel', 'EXCEL\EstudiantFullController@estudiantFull')->name('administracion.estudiants.list.full.dw.excel');
});


?>
