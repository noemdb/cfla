<table class="table table-striped table-sm table-hover">
    <thead class="thead-light">
        <tr>
            <th>Código</th>
            <th>Abreviación</th>
            <th>Nombre</th>
            <th>Orden</th>
            <th>H.Teóricas</th>
            <th>H.Prácicas</th>
            <th>Prelaciones</th>
            <th title="Número de Planes de Evalución asignados">N.P.E.Asignados</th>
            <th title="Número de Planes de Evalución cargados">N.P.E.Cargados</th>
            <th>Eliminar</th>
        </tr>
        </thead>
        <tbody>
            @foreach ($pensums as $pensum)
            <tr data-id="{{$pensum->id}}">
                <td>{{$pensum->asignatura->code ?? ''}}</td>
                <td>{{$pensum->asignatura->code_sm ?? ''}}</td>
                <td>{{$pensum->asignatura->name ?? ''}}</td>
                <td>{{$pensum->asignatura->order ?? ''}}</td>
                <td>{{$pensum->asignatura->hour_t_week ?? ''}}</td>
                <td>{{$pensum->asignatura->hour_p_week ?? ''}}</td>
                <td>{{$pensum->asignatura->prelacions ?? ''}}</td>
                <td>{{ ( !empty($pensum->pevaluacions->count()) ) ? $pensum->pevaluacions->count() : 0 }}</td>
                <td>{{ $pensum->getCountPevaluacions() ?? 0 }}</td>
                <td>
                @php $disabled = ($pensum->pevaluacions->isNotEmpty()) ? ' disabled ': null ; @endphp
                <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{$disabled ?? ''}}" href="#" id="btn-destroy_id_{{$pensum->id}}">
                        <i class="fas fa-trash"></i>
                    </a>
                </td>
            </tr>
            @endforeach
        </tbody>
</table>

{!! Form::open(['route' => ['administracion.configuraciones.pensums.destroy',':PENSUM_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/pensums/destroy.js") }}"></script>
@endsection
