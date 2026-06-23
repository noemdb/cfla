<?php

Route::get('/collections/coll_politicals/send/mail/{representant_id}/{messege_id}', 'Email\CollectionController@collectionSend')->name('email.collections.coll_politicals');

Route::get('/collections/coll_politicals/bacth/send/mail/{id}', 'Email\CollectionController@bacthCollectionSend')->name('email.collections.coll_politicals.bacth.send.mail');





///home/nuser/code/s2021/app/Http/Controllers/Administracion/Email/ContactController
