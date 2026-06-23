@php ($chart = ['range'=>1,'id_chart'=>'actividades_id_'.Auth::user()->id,'urlapi'=>route('administracion.evaluacions.actividades.chart'),'tipo'=>'line','limit'=>10,'legend'=>true,'y_axes'=>'Cantidad','x_axes'=>'Días'])
@section('scripts')
    @parent
    {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
    <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}'); </script>
@endsection

@component('administracion.elements.card.panel')
    @slot('class', 'dark')
    @slot('panelControls', 'true')
    @slot('id', $chart['id_chart'])
    @slot('header','Planificación de la aplicación de las evaluciones')
    @slot('iconTitle', $icon_menus['chartline'])
    @slot('body')
        @component('administracion.elements.canvas.chart')
            @slot('class', 'borderRBL')
            @slot('nav')
                <nav class="nav nav-tabs ranges" id="nav-tab" role="tablist" data-canvas="{{ $chart['id_chart'] ?? ''}}" data-urlapi="{{ $chart['urlapi'] ?? ''}}" data-tipo="{{ $chart['tipo'] ?? ''}}" data-limit="{{ $chart['limit'] ?? ''}}">
                    <a data-range="1" class="nav-item nav-link active" id="nav-1-tab" data-toggle="tab" href="#nav-1" role="tab" aria-controls="nav-3" aria-selected="false"> <span class="small font-weight-bold">1er Lapso</span></a>
                    <a data-range="2" class="nav-item nav-link" id="nav-2-tab" data-toggle="tab" href="#nav-6" role="tab" aria-controls="nav-2" aria-selected="false"> <span class="small font-weight-bold">2do Lapso</span></a>
                    <a data-range="3" class="nav-item nav-link" id="nav-3-tab" data-toggle="tab" href="#nav-9" role="tab" aria-controls="nav-3" aria-selected="false"> <span class="small font-weight-bold">3er Lapso</span></a>
                    <a data-range="" class="nav-item nav-link" id="nav-1-tab" data-toggle="tab" href="#nav-1" role="tab" aria-controls="nav-3" aria-selected="false"> <span class="small font-weight-bold">General</span></a>
                </nav>
            @endslot
            @slot('id', $chart['id_chart'])
        @endcomponent
    @endslot
@endcomponent
