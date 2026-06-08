<div class="text-right font-weight-bold card bd-callout bd-callout-dark px-1">
    <p class="text-dark">Resumen</p>
    <small class="px-1">

        <dl class="mb-1 ">
            <dt>Fecha de registro</dt>
            <dd class="text-secondary">{{ (!empty($oinstitucion->created_at)) ? f_date($oinstitucion->created_at) : null }}</dd>
        </dl>
        <dl class="mb-1 ">
            <dt>Última modificación</dt>
            <dd class="text-secondary">{{ (!empty($oinstitucion->updated_at)) ? f_date($oinstitucion->updated_at) : null }}</dd>
        </dl>
        
        <dl class="mb-1 ">
            <dt>Últimas 4 instituciones registradas</dt>
            @foreach ($oinstitucions->take(4) as $oinstitucion)
                <dd class="text-secondary">{{ $oinstitucion->name ?? '' }}</dd>
            @endforeach
        </dl>
        {{-- <dl class="mb-1 ">
            <dt>Institución</dt>
            <dd class="text-secondary">{{ Session::get('institucion_name') ?? '' }}</dd>
        </dl>
        <dl class="mb-1 ">
            <dt>Autoidad</dt>
            <dd class="text-secondary">{{ $autoridad4->position ?? '' }} - {{ $autoridad4->fullname ?? '' }}</dd>
        </dl>
        <hr>
        <dl class="mb-1 ">
            <dt>N. Planes de Pago registrados</dt>
            <dd class="text-secondary">{{ $planpagos->count() ?? '' }}</dd>
        </dl> --}}
    </small>
</div>
