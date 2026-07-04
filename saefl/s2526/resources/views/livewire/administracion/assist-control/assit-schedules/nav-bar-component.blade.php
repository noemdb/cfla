<div>

    <nav class="table-light">
        <div class="nav nav-tabs nav-fill" id="nav-tab" role="tablist">
            @foreach($assit_schedules as $assit_schedule)

                @if (empty($assit_schedule_id))
                    @php $active = ($loop->iteration==1) ? 'active':null ; $selected = ($loop->iteration==1) ? 'true':'false' ; @endphp
                @else
                    @php $active = ($assit_schedule->id == $assit_schedule_id) ? 'active':null ;  $selected = ($assit_schedule->id == $assit_schedule_id) ? 'true':'false' ;@endphp
                @endif

                <a class="nav-item nav-link {{$active ?? null}}" wire:click="$emit('updateAssitScheduleWorker')" id="nav-header-tab-assit_schedule-{{$assit_schedule->id}}" data-toggle="tab" href="#nav-content-assit_schedule-{{$assit_schedule->id}}" role="tab" aria-controls="nav-assit_schedule" aria-selected="{{$selected ?? null}}">
                    {{$loop->iteration}}
                    {{-- <button type="button" class="btn btn-light">{{$loop->iteration}}</button> --}}
                    {{-- <span class="badge badge-dark">{{$loop->iteration}}</span> --}}
                    <div class="small font-weight-bold">{{$assit_schedule->name ?? ''}}</div>
                </a>
            @endforeach
                <a class="nav-item nav-link" id="nav-header-tab-assit_schedule-new" data-toggle="tab" href="#nav-content-assit_schedule-new" role="tab" aria-controls="nav-assit_schedule-new" aria-selected="false">
                    <span class="badge badge-primary"><i class="{{$icon_menus['nuevo'] ?? null}}" aria-hidden="true"></i></span>
                    <span class="d-block font-weight-bolder">Nuevo</span>
                </a>
        </div>
    </nav>

</div>
