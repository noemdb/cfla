@php ($chart = ['range'=>'Todos','id_chart'=>'loginoutsrolschart','urlapi'=>route('controls.charts.audits.usages.loginoutsrols'),'tipo'=>'bar','limit'=>6 ])
@section('scripts')
    @parent
    {{-- Llamado a la funcion responsable de inicilizar el Chart --}}
    <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit'] }}'); </script>
@endsection

@component('admin.elements.card.panel')
    @slot('class', 'dark')
    @slot('panelControls', 'true')
    @slot('id', $chart['id_chart'])
    {{-- @slot('header', 'Usuarios Act/Des') --}}
    @slot('header', 'Accesos al SAEFL por Roles')
    @slot('iconTitle', $icon_menus['chartline'])
    @slot('body')
        @component('admin.elements.canvas.chart')
            @slot('class', 'borderRBL')
            @slot('nav')
                <nav class="nav nav-tabs ranges" id="nav-tab" role="tablist" data-canvas="{{ $chart['id_chart'] ?? ''}}" data-urlapi="{{ $chart['urlapi'] ?? ''}}" data-tipo="{{ $chart['tipo'] ?? ''}}" data-limit="{{ $chart['limit'] ?? ''}}">
                  <a data-range="Todos" class="nav-item nav-link active" id="nav-todos-tab" data-toggle="tab" href="#nav-todos" role="tab" aria-controls="nav-todos" aria-selected="false">Todos</a>
                  <a data-range="12" class="nav-item nav-link" id="nav-12-tab" data-toggle="tab" href="#nav-12" role="tab" aria-controls="nav-12" aria-selected="false">Últ. 12 Meses</a>
                  <a data-range="9" class="nav-item nav-link" id="nav-9-tab" data-toggle="tab" href="#nav-9" role="tab" aria-controls="nav-9" aria-selected="false">Últ. 9 Meses</a>
                  <a data-range="6" class="nav-item nav-link" id="nav-6-tab" data-toggle="tab" href="#nav-6" role="tab" aria-controls="nav-6" aria-selected="false">Últ. 6 Meses</a>
                  {{-- <a data-range="6" class="nav-item nav-link" id="nav-6-tab" data-toggle="tab" href="#nav-6" role="tab" aria-controls="nav-6" aria-selected="false">6M</a> --}}
                  <a data-range="3" class="nav-item nav-link" id="nav-3-tab" data-toggle="tab" href="#nav-3" role="tab" aria-controls="nav-3" aria-selected="false">Últ. 3 Meses</a>
                </nav>
            @endslot
            @slot('id', $chart['id_chart'])
        @endcomponent
    @endslot
@endcomponent
