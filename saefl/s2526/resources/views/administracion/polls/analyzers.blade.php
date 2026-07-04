@extends('administracion.layouts.dashboard.app')

@section('title') Procesos de Consulta - {{ Auth::user()->rol ?? '' }} @endsection

@section('main')

    <main role="main" class="col-md-10 col-lg-10">

        <div class="card card-primary mt-2">

            <div class="card-header  alert-success">
                <h4>
                    <strong>Informes de Participación en los Proceso de Consulta</strong>
                    <span class="small font-weight-bold">[{{Auth::user()->username}}]</span>

                </h4>
            </div>

            <div class="card-body">

                @include('administracion.polls.form.searchPoll',['route'=>'administracion.polls.analyzers'])

                @if ($poll_main)

                    {{-- <hr class="py-2"> --}}

                    <div class="p-4 h4">
                        {{ $poll_main->name ?? null }} <span class="text-muted">: <i>{{ $poll_main->description ?? null }}<i></span>
                    </div>

                    @include('administracion.polls.indicators.main')

                @endif

            </div>
        </div>
        {{-- <livewire:administracion.poll.index-component /> --}}
    </main>

@endsection



@section('scripts')
    @parent
    <script src="{{ asset("js/Chart.bundle.js") }}"></script>
    <script src="{{ asset("js/ChartFunction.js") }}"></script>{{-- Funciones para generar los Chart --}}
    <script src="{{ asset("js/ChartEvent.js") }}"></script>{{-- Funciones para generar los Chart --}}
@endsection
