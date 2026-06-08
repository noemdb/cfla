@php
    $chart = [
        'range'=>'7',
        'id_chart'=>'genderxplan_id_'.Auth::user()->id,
        'urlapi'=>route('administracion.configuraciones.exchange_rates.movimientocambiario.chart'),
        'tipo'=>'line',
        'limit'=>6,
        'legend'=>true,
        'y_axes'=>'Monto',
        'x_axes'=>'Fecha [dd-mm]'
    ]
@endphp

{{-- @php ($chart = ['range'=>'Todos','id_chart'=>'rolsrangeschart','urlapi'=>route('rols.ranges.chart'),'tipo'=>'pie','limit'=>6 ]) --}}
@section('scripts')
    @parent
    {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
    <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}','{{ $chart['legend'] }}','{{ $chart['y_axes'] }}','{{ $chart['x_axes'] }}'); </script>
@endsection

@component('admin.elements.card.panel')
    @slot('class', 'primary')
    @slot('panelControls', 'true')
    @slot('id', $chart['id_chart'])
    @slot('header', 'Fluctuación Cambiaria')
    @slot('iconTitle', $icon_menus['chartline'])
    @slot('body')
        @component('admin.elements.canvas.chart')
            @slot('class', 'borderRBL')
            @slot('nav')
                <nav class="nav nav-tabs ranges" id="nav-tab" role="tablist" data-canvas="{{ $chart['id_chart'] ?? ''}}" data-urlapi="{{ $chart['urlapi'] ?? ''}}" data-tipo="{{ $chart['tipo'] ?? ''}}" data-limit="{{ $chart['limit'] ?? ''}}" data-legent="{{ $chart['legent'] ?? ''}}" data-y_axes="{{ $chart['y_axes'] ?? ''}}" data-x_axes="{{ $chart['x_axes'] ?? ''}}">
                    <a data-range="7" class="nav-item nav-link active" id="nav-7-tab" data-toggle="tab" href="#nav-7" role="tab" aria-controls="nav-7" aria-selected="false">7 Días</a>
                    <a data-range="15" class="nav-item nav-link" id="nav-15-tab" data-toggle="tab" href="#nav-15" role="tab" aria-controls="nav-15" aria-selected="false">15 Días</a>
                    <a data-range="30" class="nav-item nav-link" id="nav-30-tab" data-toggle="tab" href="#nav-30" role="tab" aria-controls="nav-30" aria-selected="false">30 Días</a>
                    <a data-range="60" class="nav-item nav-link" id="nav-60-tab" data-toggle="tab" href="#nav-60" role="tab" aria-controls="nav-60" aria-selected="false">60 Días</a>
                    <a data-range="90" class="nav-item nav-link" id="nav-90-tab" data-toggle="tab" href="#nav-90" role="tab" aria-controls="nav-90" aria-selected="false">90 Días</a>
                    <a data-range="120" class="nav-item nav-link" id="nav-120-tab" data-toggle="tab" href="#nav-120" role="tab" aria-controls="nav-120" aria-selected="false">120 Días</a>
                    <a data-range="180" class="nav-item nav-link" id="nav-180-tab" data-toggle="tab" href="#nav-180" role="tab" aria-controls="nav-180" aria-selected="false">180 Días</a>
                    <a data-range="365" class="nav-item nav-link" id="nav-365-tab" data-toggle="tab" href="#nav-365" role="tab" aria-controls="nav-365" aria-selected="false">365 Días</a>
                    <a data-range="Todos" class="nav-item nav-link" id="nav-todos-tab" data-toggle="tab" href="#nav-todos" role="tab" aria-controls="nav-todos" aria-selected="false">Todos</a>
                </nav>
            @endslot
            @slot('id', $chart['id_chart'])
        @endcomponent
    @endslot
@endcomponent
