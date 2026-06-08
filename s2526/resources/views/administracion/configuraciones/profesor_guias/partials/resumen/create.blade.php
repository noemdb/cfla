<div class="text-right font-weight-bold card bd-callout bd-callout-dark px-1">
    <p class="text-dark">Resumen</p>
    <small class="px-1">
        <dl class="mb-1 ">
            <dt>Últimos 4 profesores guías registrados</dt>
            @foreach ($profesor_guias->take(4) as $profesor_guia)
                <dd class="text-secondary">{{ $profesor_guia->profesor->fullname ?? '' }}</dd>
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
