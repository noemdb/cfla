{{-- {{$assit_schedules}} --}}

<span class="p-2">
    <i class="{{ $icon_menus['assit_schedules'] ?? ''}} fa-1x text-info"></i>
    <b>Horarios de Trabajos Registrados</b>
</span>

<nav>
    <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
        @foreach($assit_schedules as $assit_schedule)
            <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}}" id="nav-header-tab-seguimiento-{{$assit_schedule->id}}" data-toggle="tab" href="#nav-content-seguimiento-{{$assit_schedule->id}}" role="tab" aria-controls="nav-home" aria-selected="true">{{$loop->iteration}}. <b>{{$assit_schedule->name ?? ''}}</b></a>
        @endforeach
    </div>
</nav>

<div class="tab-content border border-top-0" id="nav-tabContent">
    @foreach($assit_schedules as $assit_schedule)
        <div class="tab-pane fade show {{($loop->first) ? 'active':''}} shadow" id="nav-content-seguimiento-{{$assit_schedule->id}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$assit_schedule->id}}">

            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <livewire:administracion.assist-control.assit-schedules.index-component :id="$assit_schedule->id" />
                    </div>
                </div>
            </div>

            <div class="container-fluid">
                <div class="row">
                    <div class="col">
                        <p>Listado de Trabajadores</p>
                        <hr>
                        @php $workers = $assit_schedule->workers;  @endphp
                        <livewire:administracion.assist-control.assit-worker.index-component :id="$assit_schedule->id" />
                        {{-- @include('livewire.administracion.assist-control.assit-schedules.tab.workers') --}}
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>
