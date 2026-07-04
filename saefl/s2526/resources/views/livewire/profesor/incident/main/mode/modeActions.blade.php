<div>

    <div class="p-1 m-1 border rounded shadow">

        <h5 class="alert-warning py-3 px-2 text-dark font-weight-bolder rounded">
            Agregar correctivo a la <b>Incidencia</b>
            <button type="button" class="close" wire:click='close()'>
                <span aria-hidden="true">×</span>
            </button>
        </h5>

        <div class=" p-2 m-2">

            <div class="text-right font-weight-bold text-sm">
                <div class="text-sm">{{$estudiant->fullname ?? null}}</div>
                <div class=" text-sm text-muted">{{$estudiant->ci_estudiant}}</div>
                <div class="{{$estudiant->inscripcion->seccion->grado->class_text_color ?? 'default'}}">
                    {{$estudiant->inscripcion->seccion->grado->name ?? ''}} {{$estudiant->inscripcion->seccion->name ?? ''}}
                </div>
            </div>

            <hr>
            @include('livewire.profesor.incident.main.form.corrective.fields')

            <hr>

            <ul class="list-group">
                <li class="list-group-item list-group-item-secondary font-weight-bold">
                    Correctivos pedagógicos registtrados para esta incidencia.
                </li>
                @foreach ($incident_actions as $item)
                    @php $corrective = $item->incident_corrective ; @endphp
                    <li class="list-group-item">
                        <div class="d-flex justify-content-between">
                            <div> {{$corrective->description ?? null}} </div>
                            <div>
                                <button class="btn btn-danger btn-sm {{($incident->status_notify) ? ' disabled ' : null}}" role="button" wire:click="deleteAction({{$item->id}})">
                                    <i class="fa fa-trash" aria-hidden="true"></i>
                                </button>                                
                            </div>                            
                        </div>                        
                    </li>    
                @endforeach
            </ul>

        </div>

    </div>

</div>
