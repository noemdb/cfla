<?php

/*fill select list form*/
Route::get('/ajax/select/gradoByseccion/{id}', 'Ajax\FillSelectController@gradoByseccion')->name('administracion.ajax.fill.gradoByseccion');
Route::get('/ajax/select/gradoBypensum/{id}', 'Ajax\FillSelectController@gradoBypensum')->name('administracion.ajax.fill.gradoBypensum');
Route::get('/ajax/select/studiantBytype/{type}', 'Ajax\FillSelectController@studiantBytype')->name('administracion.ajax.fill.studiantBytype');
Route::get('/ajax/select/cuentaByconcepto/{id}', 'Ajax\FillSelectController@cuentaByconcepto')->name('administracion.ajax.fill.cuentaByconcepto');

//test el datatable dinamico
Route::get('/ajax/fill/datatable/abonos', 'Ajax\FillDataTableController@DataTableAbonos')->name('administracion.ajax.fill.datatable.abono');


/* validate field excist */
Route::get('/ajax/validate/exist', 'Ajax\ValidateExistController@studiant_ci')->name('administracion.ajax.validate.exist.studiant_ci');
Route::get('/ajax/validate/exist/profesor', 'Ajax\ValidateExistController@ci_profesor')->name('administracion.ajax.validate.exist.ci_profesor');


Route::post('/ajax/modal/create', 'Ajax\ModalCreateController@create_nom_concepto')->name('administracion.ajax.modal.create.nom_concepto');
