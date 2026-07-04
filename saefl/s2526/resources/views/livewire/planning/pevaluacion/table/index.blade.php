@php
    $now = \Carbon\Carbon::now();
@endphp

<div class="table-responsive">
    <table class="table table-hover table-sm small" id="pevaluacion-table">

        <thead class="thead-light">
            <tr>
                <th scope="col" class="text-center">#</th>
                <th scope="col">Plan de Estudio</th>
                <th scope="col">Asignatura / Grado-Sección / Momento</th>
                <th scope="col" class="text-center">Actividades</th>
                <th scope="col">Profesor</th>
                <th scope="col">Descripción</th>
                <th scope="col">Área de Formación</th>
                <th scope="col" class="text-center">Acciones</th>
            </tr>
        </thead>

        <tbody>
            @forelse($pevaluacions as $index => $item)
                @php
                    $pestudio = $item->pestudio;
                    $profesor = $item->profesor;
                    $pensum = $item->pensum;
                    $grado = $pensum?->grado;
                    $seccion = $item->seccion;
                    $lapso = $item->lapso;
                    $activities = $item->activities ?? collect();
                    $lapsoCerrado = $now->gt($lapso?->ffinal ?? now());
                    $tieneActividades = $activities->isNotEmpty();
                @endphp

                <tr class="{{ $lapsoCerrado ? 'table-warning' : '' }}">
                    <td class="text-center">{{ $pevaluacions->firstItem() + $index }}</td>

                    <td>{{ $pestudio?->name ?? '—' }}</td>

                    <td>
                        <div class="text-muted float-right">
                            <span class="badge badge-{{ $lapsoCerrado ? 'secondary' : 'primary' }} ml-1">
                                {{ $lapso?->name ?? '—' }}
                            </span>
                        </div>
                        <div><strong>{{ $pensum?->asignatura?->name ?? '—' }}</strong></div>
                        {{ $grado?->name ?? '—' }}-{{ $seccion?->name ?? '—' }}
                    </td>

                    <td class="text-center">
                        <span class="badge badge-info">{{ $activities->count() }}</span>
                    </td>

                    <td>{{ $profesor?->fullname ?? '—' }}</td>

                    <td class="text-muted">{{ Str::limit($item->description ?? '', 50) }}</td>

                    <td>{{ $item->asignatura_name_sm ?? '—' }}</td>

                    <td class="text-center">
                        <div class="btn-group btn-group-sm" role="group">
                            @if (!$lapsoCerrado)
                                <button type="button" class="btn btn-outline-warning btn-sm" title="Editar"
                                    wire:click="edit({{ $item->id }})">
                                    <i class="fas fa-edit"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-secondary btn-sm" disabled
                                    title="Lapso cerrado">
                                    <i class="fas fa-lock"></i>
                                </button>
                            @endif

                            @if (!$tieneActividades)
                                <button type="button" class="btn btn-outline-danger btn-sm" title="Eliminar"
                                    wire:click="delete({{ $item->id }})"
                                    wire:confirm="¿Está seguro de eliminar esta asignación?">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @else
                                <button type="button" class="btn btn-outline-secondary btn-sm" disabled
                                    title="No se puede eliminar: tiene actividades registradas">
                                    <i class="fas fa-trash"></i>
                                </button>
                            @endif
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-3">
                        <i class="fas fa-inbox mr-2"></i> No se encontraron asignaciones.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    {{-- {{ $pevaluacions->onEachSide(1)->links('pagination::bootstrap-4') }} --}}
    {{ $pevaluacions->links() }}

</div>
