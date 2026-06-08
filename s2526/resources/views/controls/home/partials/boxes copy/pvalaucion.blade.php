
<div class="border border-bottom-0 rounded-top">
    <span class="pl-1 pt-1"><b>1ER LAPSO</b></span>
</div>
<div class="border border-top-0 rounded-bottom pb-2">
    <div class="row">
        <div class="col-sm-2 pr-0">
            @component('controls.elements.boxes.chart')
                @slot('subtitle','PLANES DE EVALUACIONES')
                @slot('class','primary')
                @slot('icon',$icon_menus['pevaluacion'])
                @slot('total',$l1_count_pevaluacions )
                @slot('class_total','font-size-lg')
            @endcomponent
        </div>
        <div class="col-sm-2 pr-0">
            @component('controls.elements.boxes.chart')
                @slot('subtitle','NÚMERO DE EVALUACIONES')
                @slot('class','info')
                @slot('icon',$icon_menus['evaluacion'])
                @slot('total', $l1_count_evaluacions )
                @slot('class_total','font-size-md')
            @endcomponent
        </div>
        <div class="col-sm-2 pr-0" style="opacity: 0.3;">
            @component('controls.elements.boxes.chart')
                @slot('subtitle','PORCENTAJE DE NOTAS REGISTRADAS')
                @slot('class','secondary')
                @slot('icon',$icon_menus['notas'])
                @slot('total','50%' )
                @slot('class_total','font-size-md')
            @endcomponent
        </div>
        <div class="col-sm-2 pr-0" style="opacity: 0.3;">
            @component('controls.elements.boxes.chart')
                {{-- @slot('title','Total')                          --}}
                @slot('subtitle','INDICE DE APLAZADOS')
                @slot('class','danger')
                @slot('total','50%' )
                @slot('class_total','font-size-lg')
            @endcomponent
        </div>
        <div class="col-sm-2 pr-0" style="opacity: 0.3;">
            @component('controls.elements.boxes.chart')
                {{-- @slot('title','Total')                          --}}
                @slot('subtitle','INDICE DE APROBADOS')
                @slot('class','success')
                @slot('total','50%' )
                @slot('class_total','font-size-md')
            @endcomponent
        </div>
    </div>
</div>

<div class="border border-bottom-0 rounded-top">
    <span class="pl-1 pt-1"><b>2DO LAPSO</b></span>
</div>
<div class="border border-top-0 rounded-bottom pb-2">
    <div class="row">
        <div class="col-sm-2 pr-0">
            @component('controls.elements.boxes.chart')
                @slot('subtitle','PLANES DE EVALUACIONES')
                @slot('class','primary')
                @slot('icon',$icon_menus['pevaluacion'])
                @slot('total',0 )
                @slot('class_total','font-size-lg')
            @endcomponent
        </div>
        <div class="col-sm-2 pr-0">
            @component('controls.elements.boxes.chart')
                @slot('subtitle','NÚMERO DE EVALUACIONES')
                @slot('class','info')
                @slot('icon',$icon_menus['pevaluacion'])
                @slot('total', 0 )
                @slot('class_total','font-size-md')
            @endcomponent
        </div>
        <div class="col-sm-2 pr-0" style="opacity: 0.3;">
            @component('controls.elements.boxes.chart')
                @slot('subtitle','PORCENTAJE DE NOTAS REGISTRADAS')
                @slot('class','secondary')
                @slot('icon',$icon_menus['pevaluacion'])
                @slot('total','50%' )
                @slot('class_total','font-size-md')
            @endcomponent
        </div>
        <div class="col-sm-2 pr-0 disabled" style="opacity: 0.3;" >
            @component('controls.elements.boxes.chart')
                {{-- @slot('title','Total')                          --}}
                @slot('subtitle','INDICE DE APLAZADOS')
                @slot('class','danger')
                @slot('total','50%' )
                @slot('class_total','font-size-lg')
            @endcomponent
        </div>
        <div class="col-sm-2 pr-0" style="opacity: 0.3;">
            @component('controls.elements.boxes.chart')
                {{-- @slot('title','Total')                          --}}
                @slot('subtitle','INDICE DE APROBADOS')
                @slot('class','success')
                @slot('total','50%' )
                @slot('class_total','font-size-md')
            @endcomponent
        </div>
    </div>
</div>

<div class="border border-bottom-0 rounded-top">
    <span class="pl-1 pt-1"><b>3ER LAPSO</b></span>
</div>
<div class="border border-top-0 rounded-bottom pb-2">
    <div class="row">
        <div class="col-sm-2 pr-0">
            @component('controls.elements.boxes.chart')
                @slot('subtitle','PLANES DE EVALUACIONES')
                @slot('class','primary')
                @slot('icon',$icon_menus['pevaluacion'])
                @slot('total',0 )
                @slot('class_total','font-size-lg')
            @endcomponent
        </div>
        <div class="col-sm-2 pr-0">
            @component('controls.elements.boxes.chart')
                @slot('subtitle','NÚMERO DE EVALUACIONES')
                @slot('class','info')
                @slot('icon',$icon_menus['pevaluacion'])
                @slot('total', 0 )
                @slot('class_total','font-size-md')
            @endcomponent
        </div>
        <div class="col-sm-2 pr-0" style="opacity: 0.3;">
            @component('controls.elements.boxes.chart')
                @slot('subtitle','PORCENTAJE DE NOTAS REGISTRADAS')
                @slot('class','secondary')
                @slot('icon',$icon_menus['pevaluacion'])
                @slot('total','50%' )
                @slot('class_total','font-size-md')
            @endcomponent
        </div>
        <div class="col-sm-2 pr-0" style="opacity: 0.3;">
            @component('controls.elements.boxes.chart')
                {{-- @slot('title','Total')                          --}}
                @slot('subtitle','INDICE DE APLAZADOS')
                @slot('class','danger')
                @slot('total','50%' )
                @slot('class_total','font-size-lg')
            @endcomponent
        </div>
        <div class="col-sm-2 pr-0" style="opacity: 0.3;">
            @component('controls.elements.boxes.chart')
                {{-- @slot('title','Total')                          --}}
                @slot('subtitle','INDICE DE APROBADOS')
                @slot('class','success')
                @slot('total','50%' )
                @slot('class_total','font-size-md')
            @endcomponent
        </div>
    </div>
</div>

