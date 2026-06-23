@php
    $chart = [
        'range'=>'0',
        'id_chart'=>'registro_pagos_activitiesxmonth_id_'.Auth::user()->id,
        'urlapi'=>route('administracion.registro_pagos.activitiesxmonth.chart'),
        'tipo'=>'line',
        'limit'=>6,
        'legend'=>true,
        'y_axes'=>'Cantidad',
        'x_axes'=>'Día'
    ];
@endphp

@section('scripts')  @parent <script> requestData('{{ $chart['range'] }}','{{ $chart['id_chart'] }}','{{ $chart['urlapi'] }}','{{ $chart['tipo'] }}','{{ $chart['limit']}}','{{ $chart['legend'] }}'); </script> @endsection

<div class="p-2">

    @component('administracion.elements.card.panel')
        @slot('class', 'dark')
        @slot('panelControls', 'true')
        @slot('id', $chart['id_chart'])
        @slot('header','Cantidad diaria de registro de pagos por mes')
        @slot('iconTitle', $icon_menus['chartline'])
        @slot('body')
            @component('administracion.elements.canvas.chart')
                @slot('class', 'borderRBL')
                @slot('nav')
                    <nav class="nav nav-tabs ranges" id="nav-tab" role="tablist" data-canvas="{{ $chart['id_chart'] ?? ''}}" data-urlapi="{{ $chart['urlapi'] ?? ''}}" data-tipo="{{ $chart['tipo'] ?? ''}}" data-limit="{{ $chart['limit'] ?? ''}}">
                            
                        @php $i=0;$current = $date_start; @endphp
                        @for ($i = 0; $i < 12; $i++)
                            @php $current = $date_start->clone()->addMonths($i); @endphp
                            <a data-range="{{$i}}" class="nav-item nav-link {{($i == 0) ? 'active' : null}}" id="nav-{{$i}}-tab" data-toggle="tab" href="#nav-{{$i}}" role="tab" aria-controls="nav-{{$i}}" aria-selected="false"><span class=" small font-weight-bolder text-capitalize">{{$current->format('M Y')}}</span> </a>                        
                        @endfor
                        <a data-range="Todos" class="nav-item nav-link" id="nav-todos-tab" data-toggle="tab" href="#nav-todos" role="tab" aria-controls="nav-todos" aria-selected="false">Todos</a>
                    </nav>
                @endslot
                @slot('id', $chart['id_chart'])
            @endcomponent
        @endslot
    @endcomponent

</div>