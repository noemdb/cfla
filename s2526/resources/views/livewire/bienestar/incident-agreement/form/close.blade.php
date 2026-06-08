<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert-warning py-3 px-2 text-dark font-weight-bolder rounded">
            <button type="button" class="close" wire:click='close()'>  <span aria-hidden="true">×</span> </button>
            Cerrar <b>Incidencia registrada</b>
        </h5>

        <div class=" p-2 m-2">

            <div class="text-right font-weight-bold text-sm">
                <div class="text-sm">{{$estudiant->fullname ?? null}}</div>
                <div class=" text-sm text-muted">{{$estudiant->ci_estudiant}}</div>
                <div class="{{$estudiant->grado->class_text_color ?? 'default'}}">
                    {{$estudiant->grado->name ?? ''}} {{$estudiant->seccion->name ?? ''}}
                </div>                        
            </div>

            <hr>

            @include('livewire.bienestar.incident-agreement.form.close.fields')

            <div class="btn-group btn-block btn-group-sm" role="group" aria-label="Basic example">
                {!! Form::button('Guardar',['class' => 'form-control btn pt-1 mt-1 btn-primary','wire:click'=>"closeIncidentSave($incident->id)"]) !!}
            </div>            

        </div>

    </div>

</div>
