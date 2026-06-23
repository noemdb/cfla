<?php

/*fill select list form*/
// Route::get('/ajax/select/gradoByseccion/{id}', 'Ajax\FillSelectController@gradoByseccion')->name('profesors.ajax.fill.gradoByseccion');
// Route::get('/ajax/select/studiantBytype/{type}', 'Ajax\FillSelectController@studiantBytype')->name('administracion.ajax.fill.studiantBytype');

/*fill parials*/
// Route::get('/ajax/fill/parials/modal/registro_pago/{id}', 'Ajax\FillPartialController@RegistroPagoModal')->name('administracion.ajax.fill.modal.registro_pago');


//test el datatable dinamico
// Route::get('/ajax/fill/datatable/abonos', 'Ajax\FillDataTableController@DataTableAbonos')->name('administracion.ajax.fill.datatable.abono');


/* validate field excist */
// Route::get('/ajax/validate/exist', 'Ajax\ValidateExistController@studiant_ci')->name('administracion.ajax.validate.exist.studiant_ci');
// Route::get('/ajax/validate/exist/profesor', 'Ajax\ValidateExistController@ci_profesor')->name('administracion.ajax.validate.exist.ci_profesor');


/* modal create */
// Route::post('/ajax/modal/create', 'Ajax\ModalCreateController@create_nom_concepto')->name('administracion.ajax.modal.create.nom_concepto');
