<span class="p-1 d-block">
    <i class="{{ $icon_menus['estudiante'] ?? ''}} fa-1x text-primary"></i>
    Redimiento Estudiantíl
</span>

{{-- <div class="border rounded p-2 m-2"> --}}
    <span class="small">PRIMARIA</span>
    <div class="row">
        <div class="col">
            @component('administracion.elements.boxes.chart')
                {{-- @slot('title','Total')                          --}}
                @slot('subtitle','Índice de Aplazados')
                @slot('class','danger')
                @slot('total','50%' )
                @slot('class_total','font-size-sm')
            @endcomponent
        </div>
        <div class="col">
            @component('administracion.elements.boxes.chart')
                {{-- @slot('title','Total')                          --}}
                @slot('subtitle','Índice de Aprobados')
                @slot('class','success')
                @slot('total','50%' )
                @slot('class_total','font-size-sm')
            @endcomponent
        </div>
        <div class="col">
            @component('administracion.elements.boxes.chart')
                @slot('class','info')
                @slot('subtitle','Promédio de Notas')
                @slot('total','15pto' )
                @slot('class_total','font-size-sm')
            @endcomponent
        </div>
    </div>
{{-- </div> --}}

{{-- <div class="border rounded p-2 m-2">     --}}
    <span class="small">MEDIA GENERAL</span>
    <div class="row ">
        <div class="col">
            @component('administracion.elements.boxes.chart')
                {{-- @slot('title','Total')                          --}}
                @slot('subtitle','Índice de Aplazados')
                @slot('class','danger')
                @slot('total','50%' )
                @slot('class_total','font-size-sm')
            @endcomponent
        </div>
        <div class="col">
            @component('administracion.elements.boxes.chart')
                {{-- @slot('title','Total')                          --}}
                @slot('subtitle','Índice de Aprobados')
                @slot('class','success')
                @slot('total','50%' )
                @slot('class_total','font-size-sm')
            @endcomponent
        </div>
        <div class="col">
            @component('administracion.elements.boxes.chart')
                @slot('class','info')
                @slot('subtitle','Promédio de Notas')
                @slot('total','15pto' )
                @slot('class_total','font-size-sm')
            @endcomponent
        </div>
    </div>
{{-- </div> --}}
