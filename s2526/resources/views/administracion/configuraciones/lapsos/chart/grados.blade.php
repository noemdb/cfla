{{-- @php ($chart = ['range'=>'Todos','id_chart'=>'genderxplan_id_'.Auth::user()->id,'urlapi'=>route('administracion.administrativas.genderxplan.chart'),'tipo'=>'bar','limit'=>6,'legend'=>true,'y_axes'=>'Estudiantes','x_axes'=>'Plan Educativo']) --}}
@php ($chart = ['range'=>'Todos','id_chart'=>'grados_id_'.Auth::user()->id,'urlapi'=>route('administracion.lapso.census.chart.grado'),'tipo'=>'bar','limit'=>6,'legend'=>true,'y_axes'=>'Cantidad','x_axes'=>'Grados'])
@section('scripts')
    @parent
    {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
    <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}','{{ $chart['legend'] }}','{{ $chart['y_axes'] }}','{{ $chart['x_axes'] }}'); </script>
@endsection

@component('administracion.elements.card.panel')
    @slot('class', 'success')
    @slot('panelControls', 'true')
    @slot('id', $chart['id_chart'])
    @slot('header', 'Censo Escolar - Grados')
    @slot('iconTitle', $icon_menus['chartbar'])
    @slot('body')
        @component('administracion.elements.canvas.chart')
            @slot('class', 'borderRBL')                  
            @slot('nav') 
                <strong>&nbsp;</strong>
            @endslot
            @slot('id', $chart['id_chart'])                  
        @endcomponent
    @endslot
@endcomponent