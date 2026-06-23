<?php
/* XLS */

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

// Route::get('/excel', function () {return Excel::download(new ProductsExport, 'products.xlsx');});
Route::group(['middleware'=>['is_control']], function(){
    Route::get('/boletin_ajustes/list/excel', 'EXCEL\BoletinController@list_dw_excel')->name('administracion.boletin_ajustes.list.dw.excel');
    // Route::post('/boletin_ajustes/list/excel', 'EXCEL\AdministrativaController@list_dw_excel')->name('administracion.boletin_ajustes.list.dw.excel');
    // Route::get('/boletin_ajustes/constancia/inscripcion/pdf/{id}', 'PDF\AdministrativaController@constanciapdf')->name('administracion.boletin_ajustes.constancia.pdf');
    // Route::get('/boletin_ajustes/constancia/estudio/pdf/{id}', 'PDF\AdministrativaController@cestudiopdf')->name('administracion.boletin_ajustes.constancia.estudio.pdf');
    // Route::get('/boletin_ajustes/create/{id}', 'Tab\AdministrativaController@create')->name('administracion.boletin_ajustes.create');
});
?>
