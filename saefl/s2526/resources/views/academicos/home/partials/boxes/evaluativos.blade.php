<h6 class="p-2">
    <i class="{{ $icon_menus['estudiante'] ?? ''}} fa-1x text-primary"></i>
    Redimiento Estudiantíl
</h6>
<div class="container">
    <div class="row">
        <div class="col-sm-2 pr-0">
            @component('academicos.elements.boxes.chart')
                {{-- @slot('title','Total')                          --}}
                @slot('subtitle','INDICE DE APLAZADOS')
                @slot('class','danger')
                @slot('total','50%' )
                @slot('class_total','font-size-lg')
            @endcomponent
        </div>
        <div class="col-sm-2 pr-0">
            @component('academicos.elements.boxes.chart')
                {{-- @slot('title','Total')                          --}}
                @slot('subtitle','INDICE DE APROBADOS')
                @slot('class','success')
                @slot('total','50%' )
                @slot('class_total','font-size-md')
            @endcomponent
        </div>

        <div class="col-sm-2">
            @component('academicos.elements.boxes.chart')
                @slot('class','info')
                @slot('subtitle','PROMEDIO DE NOTAS')
                @slot('total','15pto' )
                @slot('class_total','font-size-md')
            @endcomponent
        </div>

    </div>
    <hr>
</div>
