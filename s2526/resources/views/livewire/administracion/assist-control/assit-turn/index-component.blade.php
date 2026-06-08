<div>

    <nav>
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            @foreach($assit_turns as $assit_turn)
                <a class="nav-item nav-link {{($loop->iteration==1) ? 'active':''}}" id="nav-header-tab-generales-turn-{{$assit_turn->id}}" data-toggle="tab" href="#nav-content-generales-turn-{{$assit_turn->id}}" role="tab" aria-controls="nav-assit_turn" aria-selected="{{($loop->iteration==1) ? 'true':'false'}}">
                    {{-- {{$loop->iteration}}. <b>{{$assit_turn->name ?? ''}}</b> --}}
                    <span class="badge badge-warning">{{$loop->iteration}}</span>
                    <livewire:administracion.assist-control.assit-turn.name-component :id="$assit_turn->id" :key="'assit_turn_name'.$assit_turn->id"/>
                </a>
            @endforeach
        </div>
    </nav>

    <div class="tab-content border border-top-0" id="nav-tabContent">
        @foreach($assit_turns as $assit_turn)
            <div class="tab-pane fade show {{($loop->first) ? 'active':''}} shadow" id="nav-content-generales-turn-{{$assit_turn->id}}" role="tabpanel" aria-labelledby="nav-header-assit_turn-tab-{{$assit_turn->id}}">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col">

                            <div class="p-4">

                                @php
                                    $general = 'general-assit-turn-'.$assit_turn->id;
                                    $turns = 'hours-assit-'.$assit_turn->id;
                                @endphp

                                <nav>
                                    <div class="nav nav-tabs nav-fill font-weight-bold" id="nav-tab" role="tablist">
                                        <a class="nav-item nav-link active" id="nav-{{$general}}-tab" data-toggle="tab" href="#nav-{{$general}}" role="tab" aria-controls="nav-{{$general}}" aria-selected="true">Datos Generales <small class="text-muted"> - Turnos</small></a>
                                        <a class="nav-item nav-link" id="nav-{{$turns}}-tab" data-toggle="tab" href="#nav-{{$turns}}" role="tab" aria-controls="nav-{{$turns}}" aria-selected="false">Horas</a>
                                    </div>
                                </nav>
                                <div class="tab-content border border-top-0" id="nav-tabContent">
                                    <div class="tab-pane fade show active p-4 shadow" id="nav-{{$general}}" role="tabpanel" aria-labelledby="nav-{{$general}}-tab">
                                        <livewire:administracion.assist-control.assit-turn.update-component :id="$assit_turn->id" :key="'assit_turn_update'.$assit_turn->id"/>
                                    </div>
                                    <div class="tab-pane fade p-4 shadow" id="nav-{{$turns}}" role="tabpanel" aria-labelledby="nav-profile-tab">
                                        <livewire:administracion.assist-control.assit-hour.index-component :id="$assit_turn->id" :key="'assit_turn_hour_index'.$assit_turn->id"/>
                                    </div>
                                </div>

                            </div>

                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

</div>
