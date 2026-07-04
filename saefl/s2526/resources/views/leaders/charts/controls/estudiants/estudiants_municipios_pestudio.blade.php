@php ($chart = ['range'=>'','id_chart'=>'estudiants_municipios_pestudio','urlapi'=>route('academicos.charts.control.estudiants.estudiants_municipios_pestudio'),'tipo'=>'bar','limit'=>6,'legend'=>true,'y_axes'=>'Estudiantes','x_axes'=>'Plan Educativo'])
@section('scripts')
    @parent
    {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
    <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}','{{ $chart['legend'] }}','{{ $chart['y_axes'] }}','{{ $chart['x_axes'] }}'); </script>
@endsection

@component('administracion.elements.card.panel')
    @slot('class', 'dark')
    @slot('panelControls', 'true')
    @slot('id', $chart['id_chart'])
    @slot('header', 'Distribución de estudiantes por su municipio natal por Plan de Estudio')
    @slot('iconTitle', $icon_menus['chartline'])
    @slot('body')
        @component('administracion.elements.canvas.chart')
            @slot('class', 'borderRBL')
            @slot('nav')
                <nav class="nav nav-tabs ranges" id="nav-tab" role="tablist" data-canvas="{{ $chart['id_chart'] ?? ''}}" data-urlapi="{{ $chart['urlapi'] ?? ''}}" data-tipo="{{ $chart['tipo'] ?? ''}}" data-limit="{{ $chart['limit'] ?? ''}}">
                    <a data-range="Todos" class="nav-item nav-link active" id="nav-todos-tab" data-toggle="tab" href="#nav-todos" role="tab" aria-controls="nav-todos" aria-selected="false">Todos</a>
                </nav>
            @endslot
            @slot('id', $chart['id_chart'])
        @endcomponent
    @endslot
@endcomponent
