@php ($chart = ['range'=>'15','id_chart'=>'ingresoxdia_id_'.Auth::user()->id,'urlapi'=>route('administracion.configuraciones.bancos.ingresoxdia.chart'),'tipo'=>'line','limit'=>6 ])
@section('scripts')
    @parent
    {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
    <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}'); </script>
@endsection

@component('administracion.elements.card.panel')
    @slot('class', 'primary')
    @slot('panelControls', 'true')
    @slot('id', $chart['id_chart'])
    @slot('header', 'Ingresos registrados diariamente [Divisas]')
    @slot('iconTitle', $icon_menus['chartbar'])
    @slot('body')
        @component('administracion.elements.canvas.chart')
            @slot('class', 'borderRBL')
            @slot('nav')
                <nav class="nav nav-tabs ranges" id="nav-tab" role="tablist" data-canvas="{{ $chart['id_chart'] ?? ''}}" data-urlapi="{{ $chart['urlapi'] ?? ''}}" data-tipo="{{ $chart['tipo'] ?? ''}}" data-limit="{{ $chart['limit'] ?? ''}}">
                    <a data-range="15" class="nav-item nav-link active" id="nav-15-tab" data-toggle="tab" href="#nav-15" role="tab" aria-controls="nav-3" aria-selected="false"><span class=" small font-weight-bolder">Ult. 15Días</span> </a>
                    <a data-range="30" class="nav-item nav-link" id="nav-30-tab" data-toggle="tab" href="#nav-30" role="tab" aria-controls="nav-30" aria-selected="false"><span class=" small font-weight-bolder">Últ. 30Días</span> </a>
                    <a data-range="90" class="nav-item nav-link" id="nav-90-tab" data-toggle="tab" href="#nav-9" role="tab" aria-controls="nav-90" aria-selected="false"><span class=" small font-weight-bolder">Últ. 90Días</span> </a>
                    <a data-range="120" class="nav-item nav-link" id="nav-120-tab" data-toggle="tab" href="#nav-120" role="tab" aria-controls="nav-120" aria-selected="false"><span class=" small font-weight-bolder">Últ. 120Días</span> </a>
                    {{-- <a data-range="Todos" class="nav-item nav-link" id="nav-todos-tab" data-toggle="tab" href="#nav-todos" role="tab" aria-controls="nav-todos" aria-selected="false">Todos</a> --}}
                </nav>
            @endslot
            @slot('id', $chart['id_chart'])
        @endcomponent
    @endslot
@endcomponent
