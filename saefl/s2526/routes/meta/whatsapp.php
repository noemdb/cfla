<?php

use App\Http\Controllers\WhatsAppController;

// Route::get('/webhook/sent/{phone}/{text}', [WebhookController::class, 'sendMessage']);

Route::get('/send-whatsapp/meta/template/general/{ident}/{phone}', [WhatsAppController::class, 'metaTemplateGeneralCustom']);
// Route::get('/send-whatsapp/meta/template/general/catchment/{ident}/{phone}', [WhatsAppController::class, 'metaTemplateGeneralCatchment']);
// Route::get('/send-whatsapp/meta/template/general/catchment/{ident}/{phone}', [WhatsAppController::class, 'metaTemplateGeneralCustomCatchment']);

?>
