<?php

use App\Http\Controllers\Admin\Database\FixDebateController;

Route::get('/debate/fix/clone/{source_id}/{target_id}/{index}', [FixDebateController::class, 'clone']);

?>
