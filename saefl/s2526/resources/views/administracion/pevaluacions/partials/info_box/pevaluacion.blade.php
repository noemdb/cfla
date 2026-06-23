
    <div class="row pb-2 pt-2">
        {{-- <div class="col">
            @php $total = (empty($total_1)) ? 0:$total_1 ; @endphp
            @component('administracion.elements.boxes.chart')
                @slot('title','Total')
                @slot('subtitle','PLANES DE EVALUACION ASIGNADOS')
                @slot('class','info')
                @slot('icon',$icon_menus['pevaluacion'])
                @slot('total', $total)
                @slot('progressbar', 'true')
                @slot('unidad', '%')
                @slot('class_total','font-size-md')
            @endcomponent
        </div> --}}
        <div class="col">
            @php $total = (empty($total_2)) ? 0:$total_2 ; @endphp
            @component('administracion.elements.boxes.chart')
                {{-- @slot('title','Total') --}}
                @slot('subtitle','PLANES DE EVALUACION CARGADOS')
                @slot('class','secondary')
                @slot('icon',$icon_menus['evaluacion'])
                @slot('total', $total)
                @slot('progressbar', 'true')
                @slot('unidad', '%')
                @slot('class_total','font-size-md')
            @endcomponent
        </div>
        <div class="col">
            @php $total = (empty($total_3)) ? 0:$total_3 ; @endphp
            @component('administracion.elements.boxes.chart')
                {{-- @slot('title','Total') --}}
                @slot('subtitle','NOTAS CARGADAS')
                @slot('class','primary')
                @slot('icon',$icon_menus['notas'])
                @slot('unidad', '%')
                @slot('total',$total )
                @slot('progressbar', 'true')
                @slot('class_total','font-size-md')
            @endcomponent
        </div>
    </div>
    {{-- <hr> --}}
{{-- </div> --}}
