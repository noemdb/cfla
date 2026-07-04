<?php

/* resource */

use App\Http\Controllers\Audit\Tab\HomeAuditController;

Route::get('/home', [HomeAuditController::class,'home'])->name('audits.home');


?>
