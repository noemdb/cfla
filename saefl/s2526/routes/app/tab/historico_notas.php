<?php

use App\Http\Controllers\Administracion\Tab\HistoricoNotaController;

/* resource */

Route::get('/historico_notas/index', 'Tab\HistoricoNotaController@index')->name('administracion.historico_notas.index');
// Route::get('/historico_notas/carga', 'Tab\HistoricoNotaController@carga')->name('administracion.historico_notas.carga');
Route::get('/historico_notas/crud', 'Tab\HistoricoNotaController@crud')->name('administracion.historico_notas.crud');
Route::get('/historico_notas/carga', 'Tab\HistoricoNotaController@carga')->name('administracion.historico_notas.carga');
Route::post('/historico_notas/store/carga', 'Tab\HistoricoNotaController@storeCarga')->name('administracion.historico_notas.store.carga');

Route::get('/historico_notas/create', 'Tab\HistoricoNotaController@create')->name('administracion.historico_notas.create');
Route::post('/historico_notas/store', 'Tab\HistoricoNotaController@store')->name('administracion.historico_notas.store');

Route::get('/historico_notas/show/{id}', 'Tab\HistoricoNotaController@show')->name('administracion.historico_notas.show');

Route::get('/historico_notas/edit/{id}', 'Tab\HistoricoNotaController@edit')->name('administracion.historico_notas.edit');
Route::put('/historico_notas/update/{id}', 'Tab\HistoricoNotaController@update')->name('administracion.historico_notas.update');

Route::delete('/historico_notas/destroy/{id}', 'Tab\HistoricoNotaController@destroy')->name('administracion.historico_notas.destroy');
Route::post('historico_notas/{id}/restore', [HistoricoNotaController::class, 'restore'])->name('administracion.historico_notas.restore');


// Route::get('/historico_notas/create_clone/{id}', 'Tab\HistoricoNotaController@create_clone')->name('administracion.historico_notas.create_clone');
// Route::post('/historico_notas/store_clone', 'Tab\HistoricoNotaController@store_clone')->name('administracion.historico_notas.store_clone');

?>
