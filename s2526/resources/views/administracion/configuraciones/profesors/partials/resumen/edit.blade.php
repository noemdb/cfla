<div class="text-right font-weight-bold card bd-callout bd-callout-dark px-1">
    <p class="text-dark">Resumen</p>
    <small class="px-1">

        <dl class="mb-1">
            <dt>Fecha de registro</dt>
            <dd class="text-secondary">{{ (!empty($profesor->created_at)) ? f_date($profesor->created_at) : null }}</dd>
        </dl>
        <dl class="mb-1">
            <dt>Última modificación</dt>
            <dd class="text-secondary">{{ (!empty($profesor->updated_at)) ? f_date($profesor->updated_at) : null }}</dd>
        </dl>
        <dl class="mb-1">
            <dt>Carga Académica</dt>
            <dd class="text-secondary">{{ (!empty($profesor->pevaluacions->count())) ? $profesor->pevaluacions->count() : null }} Planes de Evaluación</dd>
        </dl>
        <dl class="mb-1">
            <dt>% Índice de aprobados</dt>
            <dd class="text-secondary">{{ (!empty($profesor->getPorcAprobados())) ? $profesor->getPorcAprobados() : null }}</dd>
        </dl>

        <dl class="mb-1">
            <dt>% Carga de notas</dt>
            @php $porcentage = (!empty($profesor->goal_notas_load())) ? round((100*$profesor->real_notas_load()/$profesor->goal_notas_load()),2): null; @endphp
            <dd class="text-secondary">{{ $porcentage ?? '' }}</dd>
        </dl>
        <dl class="mb-1">
            <dt>% Carga de Plan de Evaluación</dt>
            @php $porcentage = (!empty($profesor->goal_pevaluacion_load())) ? round((100*$profesor->real_pevaluacion_load()/$profesor->goal_pevaluacion_load()),2): null; @endphp
            {{-- @php $porcentage = $profesor->goal_pevaluacion_load(); @endphp --}}
            {{-- @php $porcentage = $profesor->real_pevaluacion_load(); @endphp --}}
            <dd class="text-secondary">{{ $porcentage ?? '' }}</dd>
        </dl>

        <dl class="mb-1">
            <dt>Profesor Guía</dt>
            @foreach ($profesor->profesor_guias as $profesor_guia)
                <dd class="text-secondary pb-0 mb-0">{{ $profesor_guia->grado->name ?? '' }} {{ $profesor_guia->seccion->name ?? '' }}</dd>
            @endforeach
        </dl>
        
        <dl class="mb-1">
            <dt>Últimos 4 profesors registrados</dt>
            @foreach ($profesors->take(4) as $profesor)
                <dd class="text-secondary pb-01 mb-0">{{ $profesor->fullname ?? '' }}</dd>
            @endforeach
        </dl>
        
    </small>
</div>
