<?php

use App\Http\Controllers\Integration\WhatsAppApiController;
use App\Http\Controllers\WhatsAppController;

Route::get('/send-whatsapp/meta/template/custom/production', [WhatsAppController::class, 'production'])->name('whatsapp.meta.template.custom.production');
Route::get('/send-whatsapp/meta/template/custom/{ident}/{phone}', [WhatsAppController::class, 'metaTemplateCustom'])->name('whatsapp.meta.template.custom');
Route::get('/send-whatsapp/meta/template/custom/{ident}/{phone}/{template}', [WhatsAppController::class, 'metaTemplateCustomSendFlyer'])->name('whatsapp.meta.template.custom.send.flyer');
Route::get('/send-whatsapp/meta/custom/{ident}/{phone}', [WhatsAppController::class, 'metaCustom'])->name('whatsapp.meta.custom.ident.phone');
Route::get('/send-whatsapp/meta/index', [WhatsAppController::class, 'meta'])->name('whatsapp.meta.index');
Route::get('/send-whatsapp/meta/template/job', [WhatsAppApiController::class, 'sendMessageFlyerToJob'])->name('whatsapp.meta.template.job');
Route::get('/send-whatsapp/meta/upload/image', [WhatsAppController::class, 'uploadImage'])->name('whatsapp.meta.upload.image');

