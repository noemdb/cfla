<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert-primary py-3 px-2 text-dark font-weight-bolder rounded">
            Registrar nueva <b>Incidencia</b>
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
                <div class="text-muted">
                    PROFESOR GUIA: {{ ($estudiant_selected->profesor_guia) ? $estudiant_selected->profesor_guia->fullname : null }}
                </div>                        
            </div>

            <hr>

            @include('administracion.elements.forms.errors')

            @include('livewire.profesor.incident.main.form.fields')

            <div class="d-flex">
                <div class="flex-grow-1">
                    {!! Form::button('Guardar',['class' => 'form-control btn pt-1 mt-1 btn-primary','wire:click'=>"save()"]) !!}
                </div>
                <div wire:loading wire:target="save">
                    <div class=" d-flex align-items-center justify-content-center h-100">
                        <div class="px-4">
                            <div class="spinner-border spinner-border-sm" role="status">
                                <span class="sr-only">Procesando...</span>
                            </div>                                        
                        </div>
                    </div>                                
                </div>
            </div>

        </div>

    </div>

</div>
