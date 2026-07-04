@php ($chart = ['range'=>'3','id_chart'=>'ingresoxmetodo_id_'.Auth::user()->id,'urlapi'=>route('administracion.configuraciones.bancos.ingresoxmetodo.chart',['banco_id'=>$banco->id]),'tipo'=>'bar','limit'=>6,'legend'=>false ] )
@section('scripts')
    @parent
    {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
    <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}','{{ $chart['legend'] }}'); </script>
@endsection

@component('administracion.elements.card.panel')
    @slot('class', 'info')
    @slot('panelControls', 'true')
    @slot('id', $chart['id_chart'])
    @slot('header', 'Ingresos Por Método de Pago')
    @slot('iconTitle', $icon_menus['chartline'])
    @slot('body')
        @component('administracion.elements.chart.canvas')
            @slot('class', 'borderRBL')
            @slot('nav')
              @component('administracion.elements.chart.navyear')
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
