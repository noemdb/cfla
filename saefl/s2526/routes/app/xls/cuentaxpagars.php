<?php
/* XLS */
Route::group(['middleware'=>['is_admon']], function(){
    Route::get('/registrarpagos/export/representants/cuentaxpagars/pendientes', 'EXCEL\CuentaXPagarController@representants_cuentaxpagars_pendeintes')->name('administracion.registropagos.export.representants.cuentaxpagars.pendientes');
});
?>
