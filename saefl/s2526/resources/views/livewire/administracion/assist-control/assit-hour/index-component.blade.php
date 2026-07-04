<div>

    <nav>
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            @foreach($assit_hours as $assit_hour)
                <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}}" id="nav-header-tab-generales-hour-{{$assit_hour->id}}" data-toggle="tab" href="#nav-content-generales-hour-{{$assit_hour->id}}" role="tab" aria-controls="nav-home-assit_hour" aria-selected="true">
                    {{-- {{$loop->iteration}}. <b>{{$assit_hour->name ?? ''}}</b> --}}
                    <span class="badge badge-danger">{{$loop->iteration}}</span>
                    <livewire:administracion.assist-control.assit-hour.name-component :id="$assit_hour->id" :key="'assit_hour_name-'.$assit_hour->id"/>
                </a>
            @endforeach
        </div>
    </nav>

    <div class="tab-content border border-top-0" id="nav-tabContent">
        @foreach($assit_hours as $assit_hour)
            <div class="tab-pane fade show {{($loop->first) ? 'active':''}} shadow" id="nav-content-generales-hour-{{$assit_hour->id}}" role="tabpanel" aria-labelledby="nav-header-home-assit_hour-tab-{{$assit_hour->id}}">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">
                            <div class="p-4">
                                <livewire:administracion.assist-control.assit-hour.update-component :id="$assit_hour->id" :key="'assit_hour_update-'.$assit_hour->id"/>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
