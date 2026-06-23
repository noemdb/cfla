<?php
/* XLS */

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

// Route::get('/excel', function () {return Excel::download(new ProductsExport, 'products.xlsx');});

Route::group(['middleware'=>['is_admon']], function(){

    Route::get('/administrativas/list/excel', 'EXCEL\AdministrativaController@list_dw_excel')->name('administracion.administrativas.list.dw.excel.get');
    Route::post('/administrativas/list/excel', 'EXCEL\AdministrativaController@list_dw_excel')->name('administracion.administrativas.list.dw.excel');

});
// Route::get('/administrativas/constancia/inscripcion/pdf/{id}', 'PDF\AdministrativaController@constanciapdf')->name('administracion.administrativas.constancia.pdf');
// Route::get('/administrativas/constancia/estudio/pdf/{id}', 'PDF\AdministrativaController@cestudiopdf')->name('administracion.administrativas.constancia.estudio.pdf');
// Route::get('/administrativas/create/{id}', 'Tab\AdministrativaController@create')->name('administracion.administrativas.create');


?>
