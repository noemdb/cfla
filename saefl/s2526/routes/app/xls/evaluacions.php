<?php
/* XLS */

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

// Route::get('/excel', function () {return Excel::download(new ProductsExport, 'products.xlsx');});
Route::group(['middleware'=>['is_admon']], function(){
    Route::get('/evaluacions/list/excel', 'EXCEL\EvaluacionController@list_dw_excel')->name('administracion.evaluacions.list.dw.excel');
    // Route::post('/evaluacions/list/excel', 'EXCEL\AdministrativaController@list_dw_excel')->name('administracion.evaluacions.list.dw.excel');
    // Route::get('/evaluacions/constancia/inscripcion/pdf/{id}', 'PDF\AdministrativaController@constanciapdf')->name('administracion.evaluacions.constancia.pdf');
    // Route::get('/evaluacions/constancia/estudio/pdf/{id}', 'PDF\AdministrativaController@cestudiopdf')->name('administracion.evaluacions.constancia.estudio.pdf');
    // Route::get('/evaluacions/create/{id}', 'Tab\AdministrativaController@create')->name('administracion.evaluacions.create');
});

?>
