<?php
/* XLS */

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
Route::group(['middleware'=>['is_admon']], function(){
    Route::get('/ingresos/list/ingreso/excel', 'EXCEL\IngresoController@list_ingreso_dw_excel')->name('administracion.ingresos.list.ingreso.dw.excel');
});
?>
