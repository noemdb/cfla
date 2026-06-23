@php ($chart = ['range'=>'','id_chart'=>'deuda_representante_concepto','urlapi'=>route('controls.charts.admon.bancos.deuda_representante_concepto'),'tipo'=>'line','limit'=>6,'legend'=>true,'x_axes'=>'Porcentaje','y_axes'=>'Concepto' ])
{{-- 'tipo'=>'horizontalBar' --}}
@section('scripts')
    @parent
    {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
    <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}','{{ $chart['legend'] }}','{{ $chart['x_axes'] }}','{{ $chart['y_axes'] }}'); </script>
@endsection

@component('administracion.elements.card.panel')
    @slot('class', 'dark')
    {{-- @slot('height', '30vh') --}}
    @slot('panelControls', 'true')
    @slot('id', $chart['id_chart'])
    @slot('header', 'Porcentaje de representante deudores por conceptos')
    @slot('iconTitle', $icon_menus['chartline'])
    @slot('body')
        @component('administracion.elements.canvas.chart')
            @slot('class', 'borderRBL')
            {{-- @slot('height', '320') --}}
            @slot('nav')
            @endslot
            @slot('id', $chart['id_chart'])
        @endcomponent
    @endslot
@endcomponent
