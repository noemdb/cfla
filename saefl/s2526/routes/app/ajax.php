<?php

Route::get('/ajax/select/gradoByseccion/{id}', 'Common\Ajax\FillSelectController@gradoByseccion')->name('ajax.fill.gradoByseccion');
