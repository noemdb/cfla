@extends('profesors.layouts.dashboard.app')

@section('main')

    <main role="main">

        <div class="card card-primary mt-2 h-100">
            <div class="card-header  alert-secondary">
                <div class="btn-group float-right pt-0 pb-2">
                    @include('profesors.boletins.menus.carga')
                </div>

                <h3 class="mb-0 pb-0">
                    <i class="{{$icon_menus['notas'] ?? ''}} test-secondary" aria-hidden="true"></i>
                    Registro de Notas <b class="text-dark">POR ESTUDIANTE</b>
                </h3>

                <span class="text-muted small text-capitalize font-light">
                    {{ Auth::user()->profesor->fullname}}
                </span>

            </div>

             @if (! empty($pevaluacion) )
                @php $lapso = $pevaluacion->lapso @endphp
                <span class="px-2 text-muted">
                    Este registro esta disonible hasta: {{ ($lapso->full_date_preclosing) ? $lapso->full_date_preclosing->format('d-m-Y h:ia') : 'N.E.'}}
                </span>
            @endif

            @php $grado_color = (! empty($pevaluacion) ) ? $pevaluacion->pensum->grado->color: null; @endphp

            <div class="card-body bd-callout bd-callout-{{$grado_color ?? 'default'}} p-2 m-2">

                @if (! empty($pevaluacion) )

                    @php
                        $pevaluacion_id = $pevaluacion->id;
                        $pensum = $pevaluacion->pensum;
                        $asignatura = $pensum->asignatura;
                        $seccion = $pevaluacion->seccion;
                        $lapso = $pevaluacion->lapso;
                        $grado = $pevaluacion->pensum->grado;
                        $estudiants = $seccion->estudiants_in;

                        $profesor = Auth::user()->profesor;
                        // $grupo_estable = ($profesor) ? $pensum->grupo_estables->where('profesor_id',$profesor->id)->first() : null;
                        $grupo_estable = $pevaluacion->grupo_estable;
                    @endphp
                    

                    <div class="row  px-2 mx-1">
                        <div class="col-3 border rounded p-2">
                            <span class=" font-weight-bold text-muted">{{$grado->name ?? ''}} {{$seccion->name ?? ''}}</span>
                        </div>
                        <div class="col-3 border rounded p-2">
                            <span class=" font-weight-bold text-muted">{{$lapso->name ?? ''}}</span>

                        </div>

                        <div class="col-4 border rounded p-2 text-muted">
                            <span class=" font-weight-bold ">{{$asignatura->name ?? ''}}</span>
                            {{-- {{ $grupo_estable ?? '..'}} --}}
                            @if (!empty($grupo_estable)) <div class="text-muted text-right small font-weight-bold border-top">{{$grupo_estable->name ?? null}}</div> @endif
                        </div>

                        <div class="col-2 border rounded p-1 text-muted text-center">
                            <a class="btn btn-secondary" href="{{route('profesors.boletins.sabana.pdf',['pevaluacion_id'=>$pevaluacion->id])}}" target="_blank" role="button" title="Planilla de Registro de Notas">
                                <i class="fa fa-file-pdf" aria-hidden="true"></i>
                            </a>
                        </div>

                    </div>
                    
                    @if (!$pevaluacion->status_carga_notas)
                        <small class="small d-block text-muted">La notas cargadas no pueden ser modificadas extemporananeamente sin autorización.</small>
                    @endif

                    @include('profesors.boletins.table.carga')

                @endif

            </div>
        </div>

    </main>

@endsection




@section('scripts')
    @parent
    <script>document.title = 'SAEFL - Profesor - Registro de Notas POR ESTUDIANTE';</script>
@endsection
