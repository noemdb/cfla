

@if ($incidents_announcements->isNotEmpty())

    <div class="d-flex align-items-center">
        <div class="nav flex-column nav-pills me-3 w-25 h-100" id="v-pills-tab" role="tablist" aria-orientation="vertical">
            @foreach($incidents_announcements as $incident)
                <a class="nav-item nav-link border-bottom {{($loop->iteration==1) ? 'active':''}} px-1 font-weight-bold" id="nav-header-tab-incident-{{$incident->id}}" data-toggle="tab" href="#nav-content-incident-{{$incident->id}}" role="tab" aria-controls="nav-home" aria-selected="true"><span class="">{{ ($incident->time_announcement) ? $incident->time_announcement->format('j \d\e M \d\e Y') : 'Fecha inválida' }}</span><br> <span class=" font-weight-normal">{{ ($incident->time_announcement) ? $incident->time_announcement->format('h:i a'):null}}</span></a>
            @endforeach
        </div>
        <div class="tab-content w-75 h-100" id="v-pills-tabContent">
            @foreach($incidents_announcements as $incident)
                <div class="tab-pane fade show {{($loop->iteration==1) ? 'active':''}} py-0" id="nav-content-incident-{{$incident->id}}" role="tabpanel" aria-labelledby="nav-header-home-tab-{{$incident->id}}">
                    <div class="m-2 px-2 py-0 border rounded shadow-sm">
                        @include('livewire.bienestar.interview.mode.partials.incident_ul')
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    
@else
    <div>No hay datos</div>
@endif
