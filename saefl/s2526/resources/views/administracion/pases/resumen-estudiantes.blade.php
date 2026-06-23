<div class="card mt-4">
    <div class="card-header bg-secondary text-white">
        <h6 class="mb-0">
            <i class="fas fa-user-graduate mr-1"></i>
            Resumen de Pases por Estudiante
        </h6>
    </div>

    <div class="card-body p-0">
        @if ($resumenEstudiantes->count() === 0)
            <div class="alert alert-info text-center m-3">
                No hay datos para los filtros seleccionados
            </div>
        @else
            <div class="table-responsive">
                <table class="table table-sm table-striped table-bordered mb-0">
                    <thead class="thead-dark">
                        <tr>
                            <th>Estudiante</th>
                            <th>Plan</th>
                            <th>Grado</th>
                            <th>Sección</th>
                            <th class="text-center">Total Pases</th>
                            <th class="text-center text-success">Notificados</th>
                            <th class="text-center text-warning">Pendientes</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($resumenEstudiantes as $row)
                            <tr>
                                <td>{{ $row->estudiante }}</td>
                                <td>{{ $row->pestudio }}</td>
                                <td>{{ $row->grado }}</td>
                                <td>{{ $row->seccion }}</td>
                                <td class="text-center font-weight-bold">
                                    {{ $row->total_pases }}
                                </td>
                                <td class="text-center text-success">
                                    {{ $row->pases_notificados }}
                                </td>
                                <td class="text-center text-warning">
                                    {{ $row->pases_pendientes }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-2">
                {{ $resumenEstudiantes->links() }}
            </div>
        @endif
    </div>
</div>
