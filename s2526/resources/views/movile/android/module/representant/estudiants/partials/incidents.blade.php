@php
    $announcements = $estudiant->announcements;
    $incidents = $estudiant->incidents;
    $incidents_open = $estudiant->incidents->where('status_close',false);
    $incidents_close = $estudiant->incidents->where('status_close',true);
    $incidents_aggression = $estudiant->incidents->where('status_aggression',true);

@endphp
<ul class="list-group">
    <li class="list-group-item list-group-item-secondary">Incidencias</li>
    <li class="list-group-item px-1">
        <ul class="list-group list-group-flush small">
            <li class="list-group-item py-1 text-muted">
                <div class="d-flex justify-content-between fw-bold">
                    <div>Registradas</div>
                    <div>{{$incidents->count()}}</div>
                </div>
            </li>
            <li class="list-group-item py-1 text-muted">
                <div class="d-flex justify-content-between">
                    <div>Cerradas</div>
                    <div>{{$incidents_close->count()}}</div>
                </div>
            </li>
            <li class="list-group-item py-1 text-muted">
                <div class="d-flex justify-content-between">
                    <div>Abiertas</div>
                    <div>{{$incidents_open->count()}}</div>
                </div>
            </li>
            <li class="list-group-item py-1 text-muted">
                <div class="border-bottom pb-2 alert alert-secondary fw-bold">Agenda de convocatorias:</div>
                @forelse ($announcements as $announcement)
                    <div class="d-flex justify-content-between text-start py-2 border-bottom">
                        <div class="mx-1 fw-light"><span class="fw-bold text-muted">{{$loop->iteration}}.-</span> {{$announcement->description}}</div>
                        <div class="mx-1 text-nowrap muted">{{$announcement->time_announcement->format('d-m-Y h:i a')}}</div>
                    </div>
                @empty
                    <div class="small">No hay convocatoriás registradas.</div>
                @endforelse
            </li>
        </ul>
    </li>
</ul>
