@php
    $data = $profesor->data_indicators;
    $indicators = $data['indicators'];
    $lapsos = $data['lapsos'];
    $lapso_active = $data['lapso_active'];
@endphp

<div class="p-2">
    {{-- <i class="bi bi-bar-chart" style="font-size: 2rem"></i> --}}
    <div class="fw-bold text-dark">Indicadores</div>

    <div class="fw-bold small text-secondary pb-2 mb-2">Indicadores del desempeño en cuanto a la carga y ejecución del plan de evaluación por cada momento de evaluación.</div>
    @include('movile.android.module.profesor.indicators.pvalaucion')
</div>
