@php ($chart = ['range'=>'1','id_chart'=>'actividades_id_'.Auth::user()->id,'urlapi'=>route('directors.evaluacions.actividades.chart'),'tipo'=>'line','limit'=>6,'legend'=>true,'y_axes'=>'Estudiantes','x_axes'=>'Plan Educativo'])
@section('scripts')
    @parent
    {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
    <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}'); </script>
@endsection

@component('directors.elements.card.panel')
    @slot('class', 'success')
    @slot('panelControls', 'true')
    @slot('id', $chart['id_chart'])
    @slot('header', 'Actividad Evaluativa del Profesor PE:'.Session::get('pescolar_name'))
    @slot('iconTitle', $icon_menus['chartbar'])
    @slot('body')
        @component('directors.elements.canvas.chart')
            @slot('class', 'borderRBL')
            @slot('nav')
                <nav class="nav nav-tabs ranges" id="nav-tab" role="tablist" data-canvas="{{ $chart['id_chart'] ?? ''}}" data-urlapi="{{ $chart['urlapi'] ?? ''}}" data-tipo="{{ $chart['tipo'] ?? ''}}" data-limit="{{ $chart['limit'] ?? ''}}">
                  <a data-range="1" class="nav-item nav-link active" id="nav-1-tab" data-toggle="tab" href="#nav-1" role="tab" aria-controls="nav-3" aria-selected="false">1erL</a>
                  <a data-range="2" class="nav-item nav-link" id="nav-2-tab" data-toggle="tab" href="#nav-6" role="tab" aria-controls="nav-2" aria-selected="false">2doL</a>
                  <a data-range="3" class="nav-item nav-link" id="nav-3-tab" data-toggle="tab" href="#nav-9" role="tab" aria-controls="nav-3" aria-selected="false">3erL</a>
                </nav>
            @endslot
            @slot('id', $chart['id_chart'])
        @endcomponent
    @endslot
@endcomponent
