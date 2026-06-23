<?php

use App\Http\Controllers\General\Catchment\MainController as CatchmentController;
use App\Http\Controllers\General\Interrogation\InterviewController;
use App\Http\Controllers\General\Catchment\IndexController as CatchmentIndexController;
use App\Http\Controllers\General\Education\Competition\DebateController;
use App\Http\Controllers\General\Instrument\DiagnostigController;

// Route::group(['middleware' => ['cors']], function () {
    Route::group(['prefix' => 'general', 'namespace' => 'General'], function () {
        Route::get('/polls/{token}', 'Polls\PollMainController@index')->name('general.polls.index');
        Route::get('/preinscripcions/index/{token}', 'Preinscripcion\IndexController@index')->name('general.preinscripcions.index');
        Route::get('/enrollments/index/{token}', 'Enrollment\IndexController@index')->name('general.enrollments.index');
        Route::post('/enrollments/store', 'Enrollment\IndexController@store')->name('general.enrollments.store');
        Route::get('/send', 'Enrollment\IndexController@send')->name('general.enrollments.send');
        Route::get('/matriculations', 'Enrollment\IndexController@matriculations')->name('general.enrollments.matriculations');

        // Route::get('/enrollments/index', 'Enrollment\IndexController@index')->name('general.enrollments.index');
        // Route::get('/preinscripcions/index', 'Preinscripcion\IndexController@index')->name('general.preinscripcions.index');


Route::get('/catchments/index', function () {
    return redirect('https://uefrayluisamigosf.com/censo');
})->name('catchments.index');

Route::get('/catchments/register/{token}', function () {
    return redirect('https://uefrayluisamigosf.com/censo');
})->name('catchments.register');



        // Route::get('/catchments/index', 'Catchment\IndexController@index')->name('catchments.index');
        //Route::get('/catchments/register/{token}', 'Catchment\MainController@register' )->name('catchments.register');


        Route::get('/catchments/interview', 'Catchment\IndexController@interview')->name('catchments.interview');

        Route::get('/catchments/paper/{id}', 'Catchment\IndexController@paper')->name('catchments.paper');
        Route::get('/catchments/paper/id/{id}', 'Catchment\IndexController@paperId')->name('catchments.paper.id');
        Route::get('/catchment/paper/blank', [CatchmentIndexController::class, 'paperBlank'])->name('catchment.paper.blank');

        Route::get('/catchments/accepted/{token}', [CatchmentIndexController::class,'accepted'])->name('catchments.accepted');
        Route::get('/catchments/standby/{id}', [CatchmentIndexController::class,'standby'])->name('catchments.standby');

        // Route::get('/interviews/index', 'Catchment\IndexController@index')->name('catchments.index');

        // Route::get('/interviews/index', [InterviewController::class,'index'])->name('general.interrogation.interviews.index');

        Route::get('/educations/competitions/{tokena}/debate/{tokenb}', [DebateController::class,'index'])->name('general.educations.competitions.debate.index');
        Route::get('/educations/competitions/interactive/{token}', [DebateController::class,'interactive'])->name('general.educations.competitions.interactive.index');


        Route::get('instruments/diagnostics/index', [DiagnostigController::class,'index'])->name('general.instruments.diagnostics.index');


    });
// });


?>
