<?php
/* XLS */

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

Route::group(['middleware'=>['is_admon']], function(){
    Route::get('/representants/list/saldos/excel', 'EXCEL\RepresentantController@list_saldo_dw_excel')->name('administracion.representants.list.saldos.dw.excel');
});

Route::group(['middleware'=>['is_common']], function(){
    Route::get('/representants/list/full/excel', 'EXCEL\RepresentantFullController@representantFull')->name('administracion.representants.list.full.dw.excel');
});

?>
