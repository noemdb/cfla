<ul class="list-group">
    <li class="list-group-item list-group-item-secondary">Momentos</li>
    <li class="list-group-item">
        <div class="table-responsive table-sm small">
            <table class="table table-light">
                <thead>
                    <tr>
                        <th scope="col">Momento</th>
                        <th scope="col">Inicio</th>
                        <th scope="col">Fin</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($lapsos as $lapso)
                    <tr class="">
                        <td scope="row small">
                            <div>{{$lapso->code_sm}}</div>
                        </td>
                        <td>
                            <span class="d-block small text-muted">{{ f_date($lapso->finicial) }}</span>
                        </td>
                        <td>
                            <span class="d-block small text-muted">{{ f_date($lapso->ffinal) }}</span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

    </li>
</ul>
