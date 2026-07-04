@php ($chart = ['range'=>'Todos','id_chart'=>'genderxplan_id','urlapi'=>route('administracion.inscripcion.genderxplan.chart'),'tipo'=>'bar','limit'=>6 ])
@section('scripts')
    @parent
    {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
    <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}'); </script>
@endsection

@component('administracion.elements.card.panel')
    @slot('class', 'success')
    @slot('panelControls', 'true')
    @slot('id', $chart['id_chart'])
    @slot('header', 'Género por Plan Educativo')
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