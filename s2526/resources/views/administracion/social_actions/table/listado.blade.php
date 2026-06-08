<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th>N°</th>
            <th>Identificador</th>
            <th>Nombre</th>
            <th style="text-align: center">Servicios Ejecutados</th>
            <th style="text-align: center">Cumplimineto</th>
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody id="tdatos">

        @php $estudiants = $estudiants->sortBy('lastname'); @endphp

        @forelse ($estudiants as $item)
            @php $community_actions = $item->community_actions; @endphp
            @php $hours_completed = $item->hours_completed; @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td scope="row">{{ $item->ci_estudiant }}</td>
                <td scope="row">{{ $item->fullname }}</td>
                <td style="text-align: center">{{ $community_actions->count() ?? null }}</td>
                <td style="text-align: center">{{ $hours_completed ?? null }}</td>
                <td>
                    <div class="btn-group btn-group-sm">
                        <a name="" id="" class="btn btn-dark btn-sm"
                            href="{{ route('administracion.social_actions.estudiant.pdf', $item->id) }}" role="button"
                            target="_BLANK">
                            <i class="far fa-file-pdf"></i>
                        </a>
                    </div>
                </td>
            </tr>
        @empty
        @endforelse

    </tbody>
</table>
