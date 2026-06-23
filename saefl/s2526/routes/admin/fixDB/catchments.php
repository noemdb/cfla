<?php

    Route::get('/generate_token_accepted',function (){
        $fix_db = App\Http\Controllers\Admin\FixDB\CatchmentFixDB::generate_token_accepted();
    });

    Route::get('/import_catchment',function (){
        $fix_db = App\Http\Controllers\Admin\FixDB\CatchmentFixDB::importCatchment();
    });

?>