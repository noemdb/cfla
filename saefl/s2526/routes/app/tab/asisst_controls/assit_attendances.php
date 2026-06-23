<?php

/* assit_attendances */
Route::get('/asisst_controls/assit_attendances/index', 'AssistControl\AssitAttendanceController@index')->name('administracion.asisst_controls.assit_attendances.index');
Route::post('/asisst_controls/assit_attendances/csv/post', 'AssistControl\AssitAttendanceController@csvPost')->name('administracion.asisst_controls.assit_attendances.csv.post');
Route::post('/asisst_controls/assit_attendances/storeCSV', 'AssistControl\AssitAttendanceController@storeCSV')->name('administracion.asisst_controls.assit_attendances.storeCSV');

Route::get('/asisst_controls/assit_attendances/format', 'AssistControl\AssitAttendanceController@format')->name('administracion.asisst_controls.assit_attendances.format');
Route::get('/asisst_controls/assit_attendances/crud', 'AssistControl\AssitAttendanceController@crud')->name('administracion.asisst_controls.assit_attendances.crud');
Route::get('/asisst_controls/assit_attendances/personal', 'AssistControl\AssitAttendanceController@personal')->name('administracion.asisst_controls.assit_attendances.personal');


Route::get('/asisst_controls/assit_attendances/help/collect/csv', 'AssistControl\AssitAttendanceController@helpCollectCSV')->name('administracion.asisst_controls.assit_attendances.help.collectCSV');
Route::get('/asisst_controls/assit_attendances/help/load/csv', 'AssistControl\AssitAttendanceController@helpLoadCSV')->name('administracion.asisst_controls.assit_attendances.help.loadCSV');
Route::get('/asisst_controls/assit_attendances/help/generate/pdf', 'AssistControl\AssitAttendanceController@helpGeneratePDF')->name('administracion.asisst_controls.assit_attendances.help.GeneratePDF');

/* ------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- */
Route::get('/asisst_controls/assit_attendances/set/order/worker', 'AssistControl\AssitAttendanceController@setOrderWorker')->name('administracion.asisst_controls.assit_attendances.setOrderWorker');

?>
