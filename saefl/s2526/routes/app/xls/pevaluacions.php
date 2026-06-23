<?php
/* XLS */

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

// Route::get('/excel', function () {return Excel::download(new ProductsExport, 'products.xlsx');});
Route::group(['middleware'=>['is_control']], function(){
    Route::get('/pevaluacions/list/excel', 'EXCEL\PevaluacionController@list_dw_excel')->name('administracion.pevaluacions.list.dw.excel');
    // Route::post('/pevaluacions/list/excel', 'EXCEL\AdministrativaController@list_dw_excel')->name('administracion.pevaluacions.list.dw.excel');
    // Route::get('/pevaluacions/constancia/inscripcion/pdf/{id}', 'PDF\AdministrativaController@constanciapdf')->name('administracion.pevaluacions.constancia.pdf');
    // Route::get('/pevaluacions/constancia/estudio/pdf/{id}', 'PDF\AdministrativaController@cestudiopdf')->name('administracion.pevaluacions.constancia.estudio.pdf');
    // Route::get('/pevaluacions/create/{id}', 'Tab\AdministrativaController@create')->name('administracion.pevaluacions.create');
});

?>
