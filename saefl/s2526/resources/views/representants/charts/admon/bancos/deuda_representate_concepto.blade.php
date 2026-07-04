@php ($chart = ['range'=>'','id_chart'=>'deuda_representante_concepto','urlapi'=>route('directors.charts.admon.bancos.deuda_representante_concepto'),'tipo'=>'line','limit'=>6,'legend'=>true,'x_axes'=>'Porcentaje','y_axes'=>'Concepto' ])
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
                {{-- <nav class="nav nav-tabs ranges" id="nav-tab" role="tablist" data-canvas="{{ $chart['id_chart'] ?? ''}}" data-urlapi="{{ $chart['urlapi'] ?? ''}}" data-tipo="{{ $chart['tipo'] ?? ''}}" data-limit="{{ $chart['limit'] ?? ''}}">
                    <a data-range="3" class="nav-item nav-link active" id="nav-3-tab" data-toggle="tab" href="#nav-3" role="tab" aria-controls="nav-3" aria-selected="false"><span class=" small font-weight-bolder">Ult. 3Meses</span> </a>
                    <a data-range="6" class="nav-item nav-link" id="nav-6-tab" data-toggle="tab" href="#nav-6" role="tab" aria-controls="nav-6" aria-selected="false"><span class=" small font-weight-bolder">Últ. 6Meses</span> </a>
                    <a data-range="9" class="nav-item nav-link" id="nav-9-tab" data-toggle="tab" href="#nav-9" role="tab" aria-controls="nav-9" aria-selected="false"><span class=" small font-weight-bolder">Últ. 9Meses</span> </a>
                    <a data-range="12" class="nav-item nav-link" id="nav-12-tab" data-toggle="tab" href="#nav-12" role="tab" aria-controls="nav-12" aria-selected="false"><span class=" small font-weight-bolder">Últ. 12Meses</span> </a>
                </nav> --}}
            @endslot
            @slot('id', $chart['id_chart'])
        @endcomponent
    @endslot
@endcomponent
<span class=" small text-muted font-weight-bold">DAA: Deudas de períodos o cuotas anteriores</span>
