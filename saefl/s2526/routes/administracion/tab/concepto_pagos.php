<?php

Route::get('/concepto_pagos/dashboard', 'Tab\Configuracion\ConceptoPagoController@dashboard')->name('administracion.configuraciones.concepto_pagos.dashboard');

Route::get('/concepto_pagos/index', 'Tab\Configuracion\ConceptoPagoController@index')->name('administracion.configuraciones.concepto_pagos.index');

Route::get('/concepto_pagos/create', 'Tab\Configuracion\ConceptoPagoController@create')->name('administracion.configuraciones.concepto_pagos.create');
Route::post('/concepto_pagos/store', 'Tab\Configuracion\ConceptoPagoController@store')->name('administracion.configuraciones.concepto_pagos.store');
Route::get('/concepto_pagos/create/concept/{id}', 'Tab\Configuracion\ConceptoPagoController@create_concept')->name('administracion.configuraciones.concepto_pagos.create.concept');
// Route::post('/concepto_pagos/store/concept', 'Tab\Configuracion\ConceptoPagoController@store_concept')->name('administracion.configuraciones.concepto_pagos.store');

Route::get('/concepto_pagos/edit/{id}', 'Tab\Configuracion\ConceptoPagoController@edit')->name('administracion.configuraciones.concepto_pagos.edit');
Route::put('/concepto_pagos/update/{id}', 'Tab\Configuracion\ConceptoPagoController@update')->name('administracion.configuraciones.planpagos.update');