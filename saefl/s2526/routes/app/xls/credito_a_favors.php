<?php
/* XLS */

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;
Route::group(['middleware'=>['is_admon']], function(){
    Route::get('/credito_a_favors/list/credito/excel', 'EXCEL\CreditoAFavorController@list_credito_dw_excel')->name('administracion.creditoafavors.list.credito.dw.excel');
});
?>
