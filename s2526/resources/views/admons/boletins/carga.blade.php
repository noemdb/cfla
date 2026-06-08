@extends('directors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2 h-100">
            <div class="card-header  alert-secondary">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('directors.boletins.menus.carga')
                </div>

                <h3>
                    <i class="{{$icon_menus['notas'] ?? ''}} test-secondary" aria-hidden="true"></i>
                    Registro de Notas <b class="text-dark">POR ESTUDIANTE</b>
                </h3>

                @php $pevaluacion_id = null; @endphp
                @if (! empty($pevaluacion) )
                    @php
                        $pevaluacion_id = $pevaluacion->id;
                        $pensum = $pevaluacion->pensum;
                        $seccion = $pevaluacion->seccion;
                        $lapso = $pevaluacion->lapso;
                        $grado = $pevaluacion->pensum->grado;
                        $estudiants = $seccion->estudiants_in;
                    @endphp
                    <span>||{{$grado->name ?? ''}} {{$seccion->name ?? ''}}| {{$lapso->name ?? ''}}| Asignatura: {{$pensum->asignatura->name ?? ''}}||</span>
                @endif

            </div>

            <div class="card-body bd-callout bd-callout-{{$grado->color ?? 'default'}} p-2 m-2">

                @if (! empty($pevaluacion) )

                    @include('directors.boletins.table.carga')

                @endif

            </div>
        </div>

    </main>

@endsection




@section('scripts')
    @parent
    <script>document.title = 'SAEFL - Profesor - Registro de Notas POR ESTUDIANTE';</script>
@endsection
