{{-- <ol class="ml-1 pl-1 small text-muted">
    @foreach ($evaluacions as $evaluacion)
    <li>
        {{ $evaluacion->description ?? ''}} [{{ $evaluacion->escala->name ?? ''}} ]
        @php $disabled  = ($evaluacion->boletins->isNotEmpty()) ? ' disabled ': null ; @endphp
        <a title="Editar" class="btn-link text-dark {{$disabled ?? ''}}"  href="{{route('directors.evaluacions.edit',$evaluacion->id)}}" role="button">
            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x small"></i>
        </a>
    </li>
    @endforeach
</ol> --}}

<table class="table table-sm table-hover small">
    <tbody>
        @foreach ($evaluacions as $evaluacion)
        <tr data-id="{{$evaluacion->id}}">
            <td>
                {{$loop->iteration}}
                {{ $evaluacion->description ?? ''}} [{{ $evaluacion->escala->name ?? ''}} ]
                ||
                @php $disabled  = ($evaluacion->boletins->isNotEmpty()) ? ' disabled ': null ; @endphp
                <a title="Editar" class="btn-link text-dark {{$disabled ?? ''}}"  href="{{route('directors.evaluacions.edit',$evaluacion->id)}}" role="button">
                    <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x small"></i>
                </a>
                ||
                @php $disabled = ($evaluacion->boletins->isNotEmpty()) ? ' disabled ': null ; @endphp
                <a title="Eliminar" class="btn-destroy btn btn-link text-danger {{ $disabled }}" href="#" id="btn-destroy_id_{{$evaluacion->id}}">
                    <i class="fas fa-trash"></i>
                </a>
            </td>
        </tr>
        @endforeach
    </tbody>
</table>

{!! Form::open(['route' => ['directors.evaluacions.destroy',':EVALUACION_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts')
    @parent
    <script src="{{ asset("js/models/evaluacions/destroy.js") }}"></script>
@endsection
