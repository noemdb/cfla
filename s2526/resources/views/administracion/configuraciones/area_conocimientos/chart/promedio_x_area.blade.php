@php ($chart = ['range'=>'0','id_chart'=>'area_conocimientos_'.$pestudio->id,'urlapi'=>route('administracion.area_conocimientos.promedio_x_area.chart',['pestudio_id'=>$pestudio->id]),'tipo'=>'horizontalBar','limit'=>10,'legend'=>true,'y_axes'=>'Promedio','x_axes'=>'Áreas deConocimiento'])
@section('scripts')
    @parent
    {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
    <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}','{{ $chart['x_axes'] }}','{{ $chart['y_axes'] }}'); </script>
@endsection

@component('administracion.elements.card.panel')
    @slot('class', $pestudio->color)
    @slot('panelControls', 'true')
    @slot('id', $chart['id_chart'])
    @slot('header', $pestudio->name.' [ '.$pestudio->code.' ]')
    @slot('iconTitle', $icon_menus['chartbar'])
    @slot('body')
        @component('administracion.elements.canvas.chart')
            @slot('class', 'borderRBL')
            @slot('nav')
                <span class=" float-right small text-muted font-weight-bold">{{ 'Escala de puntuación: '.$pestudio->escala->name ?? ''}}</span>
                <nav class="nav nav-tabs ranges" id="nav-tab" role="tablist" data-canvas="{{ $chart['id_chart'] ?? ''}}" data-urlapi="{{ $chart['urlapi'] ?? ''}}" data-tipo="{{ $chart['tipo'] ?? ''}}" data-limit="{{ $chart['limit'] ?? ''}}" data-y_axes="{{ $chart['y_axes'] ?? ''}}"  data-x_axes="{{ $chart['x_axes'] ?? ''}}">
                    <a data-range="0" class="nav-item nav-link active" id="nav-1-tab" data-toggle="tab" href="#nav-1" role="tab" aria-controls="nav-3" aria-selected="false"><span class=" small font-weight-bolder">General</span></a>
                    <a data-range="1" class="nav-item nav-link" id="nav-1-tab" data-toggle="tab" href="#nav-1" role="tab" aria-controls="nav-3" aria-selected="false"><span class=" small font-weight-bolder">1er Lapso</span></a>
                    <a data-range="2" class="nav-item nav-link" id="nav-1-tab" data-toggle="tab" href="#nav-1" role="tab" aria-controls="nav-3" aria-selected="false"><span class=" small font-weight-bolder">2do Lapso</span></a>
                    <a data-range="3" class="nav-item nav-link" id="nav-1-tab" data-toggle="tab" href="#nav-1" role="tab" aria-controls="nav-3" aria-selected="false"><span class=" small font-weight-bolder">3er Lapso</span></a>
                </nav>
            @endslot
            @slot('id', $chart['id_chart'])
        @endcomponent
    @endslot
@endcomponent
