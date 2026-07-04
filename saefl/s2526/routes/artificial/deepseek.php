<?php

use App\Http\Controllers\DeepSeekController;

Route::post('/generate-text', [DeepSeekController::class, 'generateText']);


?>
