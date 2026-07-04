<?php

/* resource */

Route::get('/plan_beneficos/index', 'Tab\Configuracion\PlanBeneficoController@index')->name('administracion.configuraciones.plan_beneficos.index');

Route::get('/plan_beneficos/edit/{id}', 'Tab\Configuracion\PlanBeneficoController@edit')->name('administracion.configuraciones.plan_beneficos.edit');
Route::put('/plan_beneficos/update/{id}', 'Tab\Configuracion\PlanBeneficoController@update')->name('administracion.configuraciones.plan_beneficos.update');

Route::get('/plan_beneficos/create/{id}', 'Tab\Configuracion\PlanBeneficoController@create')->name('administracion.configuraciones.plan_beneficos.create');
Route::post('/plan_beneficos/store', 'Tab\Configuracion\PlanBeneficoController@store')->name('administracion.configuraciones.plan_beneficos.store');

Route::get('/plan_beneficos/crud', 'Tab\Configuracion\PlanBeneficoController@crud')->name('administracion.configuraciones.plan_beneficos.crud');

Route::delete('/plan_beneficos/destroy/{id}', 'Tab\Configuracion\PlanBeneficoController@destroy')->name('administracion.configuraciones.plan_beneficos.destroy');

?>