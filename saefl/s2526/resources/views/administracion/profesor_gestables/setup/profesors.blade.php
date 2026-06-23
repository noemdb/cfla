<div class="border rounded">
    <h6 class="card-title alert table-success font-weight-bold"title="Profesores Asignados a Grupos Estables">Profesores Asignados a GE</h6>
    <div class="p-1 shadow">
        <ul class="list-group list-group-flush">
            {{-- <li class="list-group-item list-group-item-success font-weight-bold" title="Profesores Asignados a Grupos Estables">Profesores Asignados a GE</li> --}}
            @forelse ($profesor_gestables as $profesor_gestable)
                @php
                    // $lapso = ($pevaluacion) ? $pevaluacion->lapso : null ;
                    $profesor = $profesor_gestable->profesor ;
                    $grupo_estable = $profesor_gestable->grupo_estable ;
                @endphp
                <li class="list-group-item small text-uppercase py-1" data-id="{{$profesor_gestable->id ?? ''}}">
                    <a name="" id="" class="btn-destroy btn btn-danger btn-sm float-right" href="#" role="button">
                        <i class="fa fa-trash" aria-hidden="true"></i>
                    </a>
                    <span class="text-muted">{{$loop->iteration}}.- </span>
                    {{ ($profesor) ? $profesor->mdname: null }}
                    <div class="pl-2 text-muted small">{{ ($profesor_gestable) ? 'Sección: '.$profesor_gestable->seccion_name: null }} {{ ($profesor_gestable) ? $profesor_gestable->lapso_name: null }}</div>
                    <div class="small pl-2 font-weight-bold text-muted" title="Nombre del Grupo Estable"> {{ ($grupo_estable) ? $grupo_estable->name: null }} </div>
                </li>
            @empty
                <div>
                    <span class="small text-muted float-right pr-2">
                        No hay Profesores asignados
                    </span>
                </div>
            @endforelse
        </ul>
    </div>
</div>


{!! Form::open(['route' => ['administracion.profesor_gestables.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
{!! Form::close() !!}
@section('scripts')  @parent <script src="{{ asset("js/models/destroy/itemList.js") }}"></script> @endsection
