<div class="card pb-2 mb-2">
    <h5 class="alert alert-secondary font-weight-bold mb-1">
        <i class="{{ $icon_menus['assit_schedules'] ?? '' }} text-dark" aria-hidden="true"></i>
        Fecha: {{ $incident->time_announcement->format('l j \d\e M \d\e Y') }}
    </h5>
    <div class="card-body py-1">
        <p class="card-text">
        <ul class="list-group list-group-flush pb-1 ">
            <li class="list-group-item py-1 pl-1 border-b-0"><span
                    class="font-weight-bold">Código</span>: BE-I{{ $incident->id ?? null }}</li>
            <li class="list-group-item py-1 pl-1 border-b-0"><span class="font-weight-bold">Estudiante:</span> {{ $estudiant->fullname ?? null }} - {{ $estudiant->fullinscripcion ?? null }}</li>
            <li class="list-group-item py-1 pl-1 border-b-0"><span class="font-weight-bold">Representante:</span> {{ $representant->name ?? null }}  </li>
            <li class="list-group-item py-1 pl-3 border-b-0"><span class="font-weight-bold">Deber</span>: {{ $incident->duty ?? null }}</li>
            <li class="list-group-item py-1 pl-3 border-b-0"><span class="font-weight-bold">Falta</span>: {{ $incident->fault ?? null }}</li>
            <li class="list-group-item py-1 pl-3 border-b-0"><span class="font-weight-bold">Descripción</span>: {{ $incident->description ?? null }} </li>
            <li class="list-group-item py-1 pl-3 border-b-0"><span class="font-weight-bold">Observaciones</span>: {{ $incident->observations ?? null }}</li>
            <li class="list-group-item py-1 pl-3 border-b-0"><span class="font-weight-bold">Acciones tomadas</span>: {{ $incident->taken_actions ?? null }}</li>
            <li class="list-group-item py-1 pl-3 border-0"><span class="font-weight-bold">F.Registro</span>: {{ $incident->created_at->format('d-m-Y') }}</li>
        </ul>
        <div class=" rounded border p-1">
            <div class=" font-weight-bold">Acuerdos</div>

            <div>
                @php $incident_agreements = $incident->incident_agreements; @endphp
                <ul class="list-group list-group-flush">
                    @forelse($incident_agreements as $agreement)
                        <li class="list-group-item py-1 border-b-0">{{ $loop->iteration ?? null }}</li>
                        <li class="list-group-item py-1 border-b-0">Descripción: {{ $agreement->description }}</li>
                        <li class="list-group-item py-1 border-b-0">Observaciones: {{ $agreement->observations }}</li>
                    @empty
                        <div>No hay datos</div>
                    @endforelse
                </ul>
            </div>
        </div>
        </p>
    </div>
</div>
