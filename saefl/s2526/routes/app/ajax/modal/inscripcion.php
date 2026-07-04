<?php

/*fill parials*/
Route::get('/ajax/fill/parials/modal/inscripcions/edit/{id}', 'Ajax\FillPartialController@inscripcionsEdit')->name('administracion.ajax.fill.modal.inscripcions.edit');
Route::get('/ajax/fill/parials/modal/inscripcions/grupo_estable/update/{id}', 'Ajax\FillPartialController@inscripcionsGrupoEstableUpdate')->name('administracion.ajax.fill.modal.inscripcions.grupo_estable.update');
