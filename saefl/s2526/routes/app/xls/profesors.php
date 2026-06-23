<?php
/* XLS */

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

// Route::get('/excel', function () {return Excel::download(new ProductsExport, 'products.xlsx');});
Route::group(['middleware'=>['is_control']], function(){
    Route::get('/profesors/list/excel', 'EXCEL\ProfesorController@list_dw_excel')->name('administracion.profesors.list.dw.excel');
    // Route::post('/profesors/list/excel', 'EXCEL\AdministrativaController@list_dw_excel')->name('administracion.profesors.list.dw.excel');
    // Route::get('/profesors/constancia/inscripcion/pdf/{id}', 'PDF\AdministrativaController@constanciapdf')->name('administracion.profesors.constancia.pdf');
    // Route::get('/profesors/constancia/estudio/pdf/{id}', 'PDF\AdministrativaController@cestudiopdf')->name('administracion.profesors.constancia.estudio.pdf');
    // Route::get('/profesors/create/{id}', 'Tab\AdministrativaController@create')->name('administracion.profesors.create');
});

?>
