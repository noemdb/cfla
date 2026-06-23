<div>

    <span class="p-2">
        <i class="{{ $icon_menus['assit_schedules'] ?? ''}} fa-1x text-info"></i>
        <b>Horarios de Trabajos Registrados</b>
    </span>

    @include('livewire.elements.messeges.oper_ok')

    <div class="row mt-3">
        <div class="col-md-3">
            <div class="mb-2">
                <button wire:click="createSchedule" class="btn btn-primary btn-block">
                    <i class="{{$icon_menus['nuevo'] ?? 'fa fa-plus'}}" aria-hidden="true"></i> Nuevo Horario
                </button>
            </div>
            
            <div class="list-group">
                @foreach($assit_schedules as $schedule)
                    <button type="button" 
                            wire:click="selectSchedule({{$schedule->id}})"
                            class="list-group-item list-group-item-action {{ $selected_schedule_id == $schedule->id ? 'active' : '' }}">
                        {{ $schedule->name }}
                        <span class="badge badge-light float-right">{{ $loop->iteration }}</span>
                    </button>
                @endforeach
            </div>
        </div>

        <div class="col-md-9">
            <div class="card shadow-sm">
                <div class="card-body">
                    @if($createMode)
                        <h5 class="card-title border-bottom pb-2">Crear Nuevo Horario</h5>
                        <livewire:administracion.assist-control.assit-schedules.form-component :createModeAssitSchedule="true" :key="'create-schedule'"/>
                    @elseif($selected_schedule_id)
                        <livewire:administracion.assist-control.assit-schedules.index-component :id="$selected_schedule_id" :key="'schedule-index-'.$selected_schedule_id" />
                        
                        <hr>
                        
                        <h6><b>Listado de Trabajadores</b> que tienen asignado este horario de trabajo.</h6>
                        <livewire:administracion.assist-control.assit-worker.update-component :id="$selected_schedule_id" :key="'assit-worker-update-'.$selected_schedule_id" />
                    @else
                        <div class="alert alert-info">
                            Seleccione un horario de la lista o cree uno nuevo.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
