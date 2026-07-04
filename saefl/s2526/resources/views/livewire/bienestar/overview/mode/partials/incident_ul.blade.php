@php
    $estudiant = $incident->estudiant;
    $representant = $estudiant->representant;
@endphp

<ul class="list-group list-group-flush font-weight-normal">
    <li class="list-group-item py-1 px-1"><span class="font-weight-bold">Código</span>: BE-I{{ $incident->id ?? null }}</li>
    <li class="list-group-item py-1 px-1"><span class="font-weight-bold">Estudiante:</span> {{ $estudiant->fullname ?? null }} - {{ $estudiant->fullinscripcion ?? null }}</li>
    <li class="list-group-item py-1 px-1"><span class="font-weight-bold">Representante:</span> {{ $representant->name ?? null }}  </li>
    <li class="list-group-item py-1 px-1"><span class="font-weight-bold">Deber</span>: {{ $incident->duty ?? null }}</li>
    <li class="list-group-item py-1 px-1"><span class="font-weight-bold">Falta</span>: {{ $incident->fault ?? null }}</li>
    <li class="list-group-item py-1 px-1"><span class="font-weight-bold">Descripción</span>: {{ $incident->description ?? null }} </li>
    <li class="list-group-item py-1 px-1"><span class="font-weight-bold">Observaciones</span>: {{ $incident->observations ?? null }}</li>
    <li class="list-group-item py-1 px-1"><span class="font-weight-bold">Acciones tomadas</span>: {{ $incident->taken_actions ?? null }}</li>
    <li class="list-group-item py-1 px-1"><span class="font-weight-bold">F.Registro</span>: {{ $incident->created_at->format('d-m-Y') }}</li>
</ul>
