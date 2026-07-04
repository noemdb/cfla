<div>

    <span class="p-2">
        <i class="{{ $icon_menus['assit_schedules'] ?? ''}} fa-1x text-info"></i>
        <b>Horarios de Trabajos Registrados</b>
    </span>

    <nav class="table-light">
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            @foreach($assit_schedules as $assit_schedule)
                <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}}" wire:click="$emit('updateAssitScheduleWorker')" id="nav-header-tab-assit_schedule-{{$assit_schedule->id}}" data-toggle="tab" href="#nav-content-assit_schedule-{{$assit_schedule->id}}" role="tab" aria-controls="nav-assit_schedule" aria-selected="{{($loop->iteration==1) ? 'true':'false'}}">
                    <span class="badge badge-dark">{{$loop->iteration}}</span>
                    <livewire:administracion.assist-control.assit-schedules.name-component :id="$assit_schedule->id" :key="'schedule_name'.$assit_schedule->id"/>
                </a>
            @endforeach
        </div>
    </nav>

    <div class="tab-content border border-top-0 table-light" id="nav-tabContent">
        @foreach($assit_schedules as $assit_schedule)
            <div class="tab-pane fade show {{($loop->first) ? 'active':''}} shadow" id="nav-content-assit_schedule-{{$assit_schedule->id}}" role="tabpanel" aria-labelledby="nav-header-assit_schedule-tab-{{$assit_schedule->id}}">

                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <livewire:administracion.assist-control.assit-schedules.index-component :id="$assit_schedule->id" :key="'assit_schedule_index'.$assit_schedule->id"/>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <h6><b>Listado de Trabajadores</b> que tienen asignado este horario de trabajo. <small class=" text-muted font-weight-bold">[{{$assit_schedule->name}}]</small></h6>
                            <livewire:administracion.assist-control.assit-worker.update-component :id="$assit_schedule->id" />
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
