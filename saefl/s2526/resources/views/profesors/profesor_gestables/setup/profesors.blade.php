<ul class="list-group">
    <li class="list-group-item list-group-item-success font-weight-bold">Profesores</li>
    @forelse ($profesor_gestables as $profesor_gestable)
        @php
            $profesor = $profesor_gestable->profesor ;
            $grupo_estable = $profesor_gestable->grupo_estable ;
        @endphp
        <li class="list-group-item small text-uppercase" data-id="{{$profesor_gestable->id ?? ''}}">
            <a name="" id="" class="btn-destroy btn btn-danger btn-sm float-right" href="#" role="button">
                <i class="fa fa-trash" aria-hidden="true"></i>
            </a>
            {{$loop->iteration}}.- {{ ($profesor) ? $profesor->mdname: null }}
            <div class="small" title="Nombre del Grupo Estable"> {{ ($grupo_estable) ? $grupo_estable->name: null }} </div>
        </li>
    @empty
        <span>No hay Profesores asignados</span>
    @endforelse
</ul>


{!! Form::open(['route' => ['administracion.profesor_gestables.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')  @parent <script src="{{ asset("js/models/destroy/itemList.js") }}"></script> @endsection
