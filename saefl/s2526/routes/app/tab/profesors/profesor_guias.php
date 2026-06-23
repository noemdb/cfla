<?php
use App\Http\Controllers\Profesor\Tab\ProfesorGuiaController;
/* resource */

Route::get('/profesor_guias/index', 'Tab\ProfesorGuiaController@index')->name('profesors.profesor_guias.index');
Route::get('/profesor_guias/crud', 'Tab\ProfesorGuiaController@crud')->name('profesors.profesor_guias.crud');

Route::get('/profesor_guias/create', 'Tab\ProfesorGuiaController@create')->name('profesors.profesor_guias.create');
Route::post('/profesor_guias/store', 'Tab\ProfesorGuiaController@store')->name('profesors.profesor_guias.store');

Route::get('/profesor_guias/edit/{id}', 'Tab\ProfesorGuiaController@edit')->name('profesors.profesor_guias.edit');
Route::put('/profesor_guias/update/{id}', 'Tab\ProfesorGuiaController@update')->name('profesors.profesor_guias.update');

Route::delete('/profesor_guias/destroy/{id}', 'Tab\ProfesorGuiaController@destroy')->name('profesors.profesor_guias.destroy');

// Ruta con dos parámetros
Route::get(
    'profesor-guias/diagnostico/{diagMain}/{seccion}',
    [ProfesorGuiaController::class, 'showDiag']
)->name('profesors.profesor_guias.diag.show');

Route::get(
    'profesor-guias/diagnostico/report/{diagMain}',
    [ProfesorGuiaController::class, 'getSectionReport']
)->name('profesors.profesor_guias.diag.report');

?>
