<?php
/* XLS */

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
Route::group(['middleware'=>['is_admon']], function(){
    Route::get('/abonos/list/abono/excel', 'EXCEL\AbonoController@list_abono_dw_excel')->name('administracion.abonos.list.abono.dw.excel');
});
?>
