<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert-warning py-3 px-2 text-dark font-weight-bolder rounded">
            Detalles internos de la <b>Incidencia</b>
            <button type="button" class="close" wire:click='close()'>
                <span aria-hidden="true">×</span>
            </button>
        </h5>

        <div class=" p-2 m-2">

            <div class="text-right font-weight-bold text-sm">
                <div class="text-sm">{{$estudiant_selected->fullname ?? null}}</div>
                <div class=" text-sm text-muted">{{$estudiant_selected->ci_estudiant}}</div>
                <div class="{{$estudiant_selected->inscripcion->seccion->grado->class_text_color ?? 'default'}}">
                    {{$estudiant_selected->inscripcion->seccion->grado->name ?? ''}} {{$estudiant_selected->inscripcion->seccion->name ?? ''}}
                </div>
            </div>

            <hr>

            @include('bienestars.incidents.pdf.profesor.datasheet')
            {{-- @include('bienestars.incidents.pdf.profesor.partials.details') --}}

        </div>

    </div>

</div>
