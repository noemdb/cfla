<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert-warning py-3 px-2 text-dark font-weight-bolder rounded">
            Editar <b>Incidencia</b>
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

            @include('administracion.elements.forms.errors')

            @include('livewire.bienestar.incident.form.fields')

            <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                {!! Form::button('Guardar',['class' => 'form-control btn pt-1 mt-1 btn-primary','wire:click'=>"save()"]) !!}
            </div>

        </div>

    </div>

</div>
