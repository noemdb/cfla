<?php
/* XLS */

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

// Route::get('/excel', function () {return Excel::download(new ProductsExport, 'products.xlsx');});
Route::group(['middleware'=>['is_control']], function(){
    Route::get('/inscripcions/list/excel', 'EXCEL\InscripcionController@list_dw_excel')->name('administracion.inscripcions.list.dw.excel');
    // Route::post('/inscripcions/list/excel', 'EXCEL\AdministrativaController@list_dw_excel')->name('administracion.inscripcions.list.dw.excel');
    // Route::get('/inscripcions/constancia/inscripcion/pdf/{id}', 'PDF\AdministrativaController@constanciapdf')->name('administracion.inscripcions.constancia.pdf');
    // Route::get('/inscripcions/constancia/estudio/pdf/{id}', 'PDF\AdministrativaController@cestudiopdf')->name('administracion.inscripcions.constancia.estudio.pdf');
    // Route::get('/inscripcions/create/{id}', 'Tab\AdministrativaController@create')->name('administracion.inscripcions.create');
});

?>
