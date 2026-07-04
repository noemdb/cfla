<div>

    {{-- @include('livewire.elements.messeges.oper_ok') --}}

    <div class="tab-content border border-top-0 table-light" id="nav-tabContent-assit_schedule">
        @foreach($assit_schedules as $assit_schedule)

            @if (empty($assit_schedule_id))
                @php $active = ($loop->iteration==1) ? 'active':null ; @endphp
            @else
                @php $active = ($assit_schedule->id == $assit_schedule_id) ? 'active':null ; @endphp
            @endif
            <div class="tab-pane fade show {{$active ?? null}} shadow" id="nav-content-assit_schedule-{{$assit_schedule->id}}" role="tabpanel" aria-labelledby="nav-header-assit_schedule-tab-{{$assit_schedule->id}}">

                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <livewire:administracion.assist-control.assit-schedules.index-component :id="$assit_schedule->id" :key="'assit-schedules-index-'.$assit_schedule->id" />
                        </div>
                    </div>
                </div>

                <hr>

                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <h6><b>Listado de Trabajadores</b> que tienen asignado este horario de trabajo. <small class=" text-muted font-weight-bold">[{{$assit_schedule->name}}]</small></h6>
                            <livewire:administracion.assist-control.assit-worker.update-component :id="$assit_schedule->id" :key="'assit-worker-update-'.$assit_schedule->id" />
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
        <div class="tab-pane fade show shadow" id="nav-content-assit_schedule-new" role="tabpanel" aria-labelledby="nav-header-assit_schedule-tab-new">
            <div class="p-2">
                <livewire:administracion.assist-control.assit-schedules.form-component :createModeAssitSchedule="true"/>
            </div>
        </div>
    </div>
</div>
