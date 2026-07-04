<?php
/* fill_exchange_rate */

Route::get('/ajax/fill/exchange/rate/ammount/{date_payment}', 'Ajax\FillPartialController@ExchangeRateAmmount')->name('administracion.ajax.fill.ExchangeRateAmmount');

?>
