<span class="p-2">
    <i class="{{ $icon_menus['estudiante'] ?? ''}} fa-1x text-primary"></i>
    Plan de Evaluación
</span>
{{-- <div class="container"> --}}
    <div class="row">
        <div class="col">
            @component('administracion.elements.boxes.chart')
                {{-- @slot('title','Total') --}}
                @slot('subtitle','PLANES DE EVALUACION REGISTRADOS')
                @slot('class','info')
                @slot('icon',$icon_menus['pevaluacion'])
                @slot('total', $total_1)
                @slot('unidad', '%')
                @slot('class_total','font-size-md')
            @endcomponent
        </div>
        <div class="col">
            @component('administracion.elements.boxes.chart')
                {{-- @slot('title','Total') --}}
                @slot('subtitle','NOTAS REGISTRADAS')
                @slot('class','primary')
                @slot('icon',$icon_menus['notas'])
                @slot('unidad', '%')
                @slot('total',$total_2 )
                @slot('class_total','font-size-md')
            @endcomponent
        </div>
    </div>
    {{-- <hr> --}}
{{-- </div> --}}
