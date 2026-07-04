<?php

Route::get('/registropago/ticket/send/mail/{id}', 'Email\RegistroPago\sendTicketPaymentController@sendMail')->name('email.registropago.ticket.send.mails');


//routes/app/email/registropago/ticket
