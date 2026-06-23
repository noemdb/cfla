@php
    $poll_answer_by_tokens = $poll_main->poll_answer_by_tokens;
    $count_poll_main_answer = $poll_answer_by_tokens->count();

    $count_tokens = $poll_main->poll_tokens->isNotEmpty() ? $poll_main->poll_tokens->count() : null;
    $participations = $poll_main->participations;

    $poll_questions = $poll_main->poll_questions;
@endphp

{{-- <hr class="p-1 m-1"> --}}

@include('administracion.polls.indicators.partials.details')

<hr>

<div class="container-fluid">

    <div class="row">
        <div class="col">
            <div class="h5 text-center font-weight-bold">Análisis de Resultados</div>
        </div>
    </div>

    <div class="row">

        <div class="col-lg-12">
            @include('administracion.polls.indicators.partials.general')
        </div>
        <div class="col-lg-12">
            @include('administracion.polls.indicators.partials.userxarea')
        </div>

        <div class="col-lg-12">
            @include('administracion.polls.indicators.partials.timeline')
        </div>

        <div class="col-lg-12">
            @include('administracion.polls.indicators.partials.tracking')
        </div>

    </div>

    <hr>

    {{-- <div class="row">
        <div class="col-12">
            @include('administracion.polls.indicators.partials.pestudios')
        </div>
    </div> --}}

    <hr>

    <div class="row">
        <div class="col-12">
            <div>Resultados de la particiación por pregunta.</div>
            <div class="row">
                @foreach ($poll_questions as $poll_question)
                    <div class="col-lg-6">
                        @include('administracion.polls.indicators.partials.question')
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-12">
            @include('administracion.polls.indicators.partials.listResult')
        </div>
    </div>

</div>
