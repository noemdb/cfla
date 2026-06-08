@php
    $chart = [
        'range'=>'7',
        'id_chart'=>'countxday_id_'.Auth::user()->id,
        'urlapi'=>route('academicos.charts.admon.payments.countxday'),
        'tipo'=>'line',
        'limit'=>6,
        'legend'=>true,
        'y_axes'=>'Cantidad',
        'x_axes'=>'Día'
    ];
@endphp

@section('scripts')
    @parent
    {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
    <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit']}}','{{ $chart['legend'] }}'); </script>
@endsection

@component('academicos.elements.card.panel')
    @slot('class', 'info')
    @slot('panelControls', 'true')
    @slot('id', $chart['id_chart'])
    @slot('header', 'Cantidad de Reportes de Pagos por día')
    @slot('iconTitle', $icon_menus['chartline'])
    @slot('body')
        @component('academicos.elements.chart.canvas')
            @slot('class', 'borderRBL')
            @slot('nav')
              @component('academicos.elements.chart.navDayAsc')
                @slot('id_chart', $chart['id_chart'])
                @slot('urlapi', $chart['urlapi'])
                @slot('tipo', $chart['tipo'])
                @slot('limit', $chart['limit'])
                @slot('legend', $chart['legend'])
              @endcomponent
            @endslot
            @slot('id', $chart['id_chart'])
        @endcomponent
    @endslot
@endcomponent
