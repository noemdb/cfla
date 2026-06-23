<?php
/* XLS */

use App\Exports\ProductsExport;
use Maatwebsite\Excel\Facades\Excel;

// Route::get('/excel', function () {return Excel::download(new ProductsExport, 'products.xlsx');});
Route::group(['middleware'=>['is_control']], function(){
    Route::get('/boletins/list/excel', 'EXCEL\BoletinController@list_dw_excel')->name('profesors.boletins.list.dw.excel');
    // Route::post('/boletins/list/excel', 'EXCEL\AdministrativaController@list_dw_excel')->name('administracion.boletins.list.dw.excel');
    // Route::get('/boletins/constancia/inscripcion/pdf/{id}', 'PDF\AdministrativaController@constanciapdf')->name('administracion.boletins.constancia.pdf');
    // Route::get('/boletins/constancia/estudio/pdf/{id}', 'PDF\AdministrativaController@cestudiopdf')->name('administracion.boletins.constancia.estudio.pdf');
    // Route::get('/boletins/create/{id}', 'Tab\AdministrativaController@create')->name('administracion.boletins.create');
});

?>
