<div>

    <h4><u title="Listado especial con botones de acción">Listado</u> de <span class="font-weight-bolder">mensaje</span> enviados por META</h4>

    <div class="float-right ">
        <small wire:loading.delay.shortest class="text-muted small px-2">
            Procesando...
        </small>
    </div>

    <div class="container-fluid">

        <div class="row">

            <div class="col">
                <div class="border rounded">

                    <div class="container-fluid">
                        <div class="row">
                            {{-- 
                            <div class="col-4">
                                <div class="form-group">
                                    <label for="help_contact" class=" font-weight-bold m-0 small">Contacto</label>
                                    {!! Form::text('help_contact', old('help_contact'), ['wire:model'=>'help_contact','class' => 'form-control','placeholder'=>'Contacto']); !!}
                                </div>
                            </div>
                            --}}
                            {{-- <div class="col">
                                <div class="form-group">
                                    <label for="contact" class=" font-weight-bold m-0 small">Listado de contactos</label>
                                    {!! Form::select('contact', $list_contact, old('contact'), [
                                        'wire:model' => 'contact',
                                        'class' => 'form-control',
                                        'id' => 'contact',
                                        'placeholder' => 'Selecciones',
                                    ]) !!}
                                </div>
                            </div> 
                            --}}
                        </div>

                        <div class="row">
                            <div class="col">
                                @include('livewire.administracion.meta.chat.table.indexws')
                            </div>
                        </div>
                    </div>                   
                    
                </div>
            </div>

        </div>

    </div>

</div>
