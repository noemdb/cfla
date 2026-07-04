<?php

/* resource */
Route::get('/boletins/crud', 'Tab\BoletinController@crud')->name('representants.boletins.crud');

Route::get('/boletins/corte', 'Tab\BoletinController@corte')->name('representants.boletins.corte');


?>
