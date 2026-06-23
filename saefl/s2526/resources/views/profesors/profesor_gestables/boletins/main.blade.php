<div class="card shadow">
    <div class="card-body p-0">
        <div class="border rounded ">
            <h5 class="card-title alert alert-secondary">
                @php
                    $fullname = $selected->fullname;
                    $fullname_sm = ($fullname) ? Str::limit(strtoupper($fullname),32): null;
                    $asignatura = ($selected) ? $selected->asignatura : null ;
                @endphp
                <span class="small float-right font-weight-bold btn btn-light btn-sm">
                    {{ ($grupo_estable) ? $grupo_estable->name : ''}}
                </span>
                <span class="small font-weight-bold" title="{{$fullname ?? null}}">
                    {{ ($asignatura) ? $asignatura->code : ''}} {{ $fullname ?? ''}}
                </span>
            </h5>
            <p class="card-text">
                <div class="p-1">
                    @php
                        $pevaluacion = $selected;
                        $grado = $pevaluacion->grado;
                        $lapso = $pevaluacion->lapso;
                        $seccion = $pevaluacion->seccion;
                        $pensum = $pevaluacion->pensum;
                        // $estudiants = $pevaluacion->getGEEstudiant($profesor->id);
                    @endphp
                    @include('profesors.profesor_gestables.boletins.carga')
                </div>
            </p>
        </div>
    </div>
</div>
