<div class=" bg-white">

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert-primary py-3 px-2 text-dark font-weight-bolder rounded">
            Registrar un nuevo <b>Correctivo Pedagógico</b>
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
            
            <div class="text-right text-sm">
                <div class="text-sm"> <span class="font-weight-bold"> Descripción de la incidencia:</span> {{$incident->description ?? null}}</div>
                {{-- <div class=" text-sm text-muted">{{$incident->observations}}</div> --}}
            </div>
            
            <hr class="">
            @include('livewire.profesor.incident.action.form.fields')

            <ul class="list-group">
                <li class="list-group-item list-group-item-secondary font-weight-bold">
                    Correctivos pedagógicos registtrados para esta incidencia:
                </li>
                @forelse ($incident_actions as $item)
                    @php $corrective = $item->incident_corrective ; @endphp
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div> {{$corrective->description ?? null}} </div>
                            <div>
                                <button class="btn btn-danger btn-sm {{($incident->status_notify) ? ' disabled ' : null}}" role="button" wire:click="delete({{$item->id}})">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>                                
                            </div>                            
                        </div>                        
                    </li>    
                @empty

                    <li class="list-group-item text-muted">No hay corrrectivos registrados.</li>
                    
                @endforelse
                
            </ul>

        </div>

    </div>

</div>
