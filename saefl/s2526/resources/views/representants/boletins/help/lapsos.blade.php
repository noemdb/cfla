<div class="card  border rounded shadow-sm border border-info">
    <h4 class="card-title py-1 my-1 text-center">
        <div>
            <div class="p-1">
                <i class="fa fa-info fa-1x text-info p-2 border border-info rounded-pill" aria-hidden="true"></i>
            </div>
        </div>
    </h4>
    <div class="card-body py-1 my-1 text-justify">
        Fechas programadas para el inicio y finalización de los Momentos de Evaluación
        <table class="table table-sm">
            <thead>
                <tr>
                    <th>Momento</th>
                    <th>Fecha de Inicio</th>
                    <th>Fecha de Finalización</th>
                    <th>Fecha de publicación Corte de Notas</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($lapsos as $lapso)
                    <tr>
                        <td># {{ $lapso->id ?? ''}}</td>
                        <td>{{ f_date($lapso->finicial) ?? ''}}</td>
                        <td>{{ f_date($lapso->ffinal) ?? ''}}</td>
                        <td>{{ f_date($lapso->date_cutnote) ?? ''}}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="3">NO HAY DATOS</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

