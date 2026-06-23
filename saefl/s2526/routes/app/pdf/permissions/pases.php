<?php
/* PDF */

use App\Http\Controllers\Permission\PDF\PaseController;

Route::get('/permissions/pases/certificate/pdf/{id}', [PaseController::class,'certificate'])->name('permissions.pases.pdf.certificate');




?>