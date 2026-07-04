
@php
    $estudiant = $incident->estudiant;
    $representant = $estudiant->representant;
@endphp

<ul class="list-group list-group-flush border-0 text-start mb-2 small">
    <li class="list-group-item py-1 px-1"><span class="fw-bold">Código</span>: BE-I{{ $incident->id ?? null }}</li>
    <li class="list-group-item py-1 px-1"><span class="fw-bold">Estudiante:</span> <div class="ms-2">{{ $estudiant->fullname ?? null }} - {{ $estudiant->fullinscripcion ?? null }}</div></li>
    <li class="list-group-item py-1 px-1"><span class="fw-bold">Representante:</span> <div class="ms-2">{{ $representant->name ?? null }}</div> </li>
    <li class="list-group-item py-1 px-1"><span class="fw-bold">Deber</span>: {{ $incident->duty ?? null }}</li>
    <li class="list-group-item py-1 px-1"><span class="fw-bold">Falta</span>: {{ $incident->fault ?? null }}</li>
    <li class="list-group-item py-1 px-1"><span class="fw-bold">Descripción</span>: <div class="ms-2">{{ $incident->description ?? null }} </div></li>
    <li class="list-group-item py-1 px-1"><span class="fw-bold">Observaciones</span>: <div class="ms-2">{{ $incident->observations ?? null }}</div></li>
    <li class="list-group-item py-1 px-1"><span class="fw-bold">Acciones tomadas</span>: <div class="ms-2">{{ $incident->taken_actions ?? null }}</div></li>
    <li class="list-group-item py-1 px-1"><span class="fw-bold">F.Registro</span>: {{ $incident->created_at->format('d-m-Y') }}</li>
</ul>
