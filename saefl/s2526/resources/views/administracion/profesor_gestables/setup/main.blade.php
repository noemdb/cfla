<div class="card mx-3 shadow">
    <div class="card-body p-0">
        <div class="card-title alert alert-secondary">
            @php
                $asignatura = $selected->asignatura;
                $grado = ($selected) ? $selected->grado : null ;
            @endphp
            <div class="font-weight-bold" title="{{$fullname ?? null}}">
                {{ ($asignatura) ? $asignatura->code : ''}} {{ $asignatura->name ?? ''}}
            </div>
            <div class="small text-muted">{{ ($grado) ? $grado->name : null}}</div>
        </div>
        <p class="card-text">
            <div class="p-2">
                {{-- @php $profesor_gestables = $selected->profesor_gestables; @endphp --}}
                @php $profesor_gestables = $selected->getProfesorGestables($pensum_seccion_id); @endphp
                @include('administracion.profesor_gestables.setup.profesors')
            </div>
            <hr>
            <div class="p-2">
                @include('administracion.profesor_gestables.setup.create')
            </div>
        </p>
</div>
</div>
