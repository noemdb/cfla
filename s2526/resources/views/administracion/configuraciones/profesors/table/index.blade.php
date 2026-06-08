@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_ci="";
    $class_gsuite="";
    $class_phone="";
    $class_username="";
    $class_action="nosort";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1 small" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_profesor }}">Tipo</th>
                <th class="{{ $class_profesor }}">Nombre</th>
                <th class="{{ $class_ci }}">Cédula</th>
                <th class="{{ $class_ci }}">Estado</th>
                <th class="{{ $class_ci }}" title="Carga académica">C. Académica</th>
                {{-- <th class="{{ $class_ci }}" title="Carga académica">A. Formación</th> --}}
                <th class="{{ $class_ci }}" title="Plan de Estudio">P. Estudio</th>
                {{-- <th class="{{ $class_ci }}">Fecha de nacimiento</th> --}}
                <th class="{{ $class_phone }}">Teléfono</th>
                <th class="{{ $class_username }}">Nombre Usuario</th>
                <th class="{{ $class_gsuite }}">Email</th>
                <th class="{{ $class_gsuite }}">GSuite</th>
                @admin<th class="{{ $class_action }}">Creado</th>@endadmin
                <th class="{{ $class_action }}">Acciones</th>
            </tr>
        </thead>

        <tbody id="tdatos">
        @foreach($profesors as $profesor)

        @php 
        $pevaluacions_count = (!empty($profesor->pevaluacions->count())) ? $profesor->pevaluacions->count():null;
        $pevaluacions = $profesor->pevaluacions ?? null;
        $pevaluacion = $profesor->pevaluacions->first() ?? null;
        $pestudio = $pevaluacion->pestudio ?? null;
        @endphp

            <tr data-id="{{$profesor->id}}" class="text-{{ ($profesor->status_active == 'false') ? 'secondary':'dark' }}">
                <td id="td-count" class="{{ $class_N }}">
                    {{$loop->iteration}}
                </td>
                <td class="{{ $class_profesor  ?? ''}}">
                    {{$profesor->ti_teacher}}
                </td>
                <td class="{{ $class_profesor  ?? ''}}">
                    {{$profesor->fullname}}
                </td>
                <td class="{{ $class_ci ?? '' }}">
                    {{ $profesor->ci_profesor ?? ''}}
                </td>
                <td class="{{ $class_ci ?? '' }}">
                    {{ ($profesor->status_active == 'false') ? 'Desactivo' : 'Activo'}}
                    {{ ($pevaluacions_count) ? '|SI|' : '|NO|'}}
                </td>
                <td class="{{ $class_ci ?? '' }}">
                    @php $n= 0; @endphp
                    @foreach ($lapsos as $lapso)                    
                        <span class="badge badge-{{ $lapso->class ?? secondary }}">
                            {{ str_pad($pevaluacions_count,2,'0',STR_PAD_LEFT) ?? '00' }}
                        </span>
                    @endforeach
                </td>

                {{-- <td>
                    <div class="text-muted small">
                        @forelse ($pevaluacions as $item)
                            <div>{{$loop->iteration}}. {{ $item->fullname ?? ''}}</div>
                        @empty
                            <div></div>
                        @endforelse
                    </div>
                </td> --}}

                <td>
                    <div class="">{{ ($pestudio) ? $pestudio->name : null }}</div>
                    <div class="text-muted">{{ ($pestudio) ? $pestudio->code : null }}</div>
                </td>

                <td class="{{ $class_phone ?? '' }}">
                    {{$profesor->phone ?? ''}}
                </td>

                <td class="{{ $class_username ?? '' }}">
                    {{ ($profesor->user) ? $profesor->user->username : null }}
                </td>
                
                <td class="{{ $class_gsuite ?? '' }}">
                    {{ $profesor->email ?? ''}}
                </td>                
                <td class="{{ $class_gsuite ?? '' }}">
                    {{ $profesor->gsemail ?? ''}}
                </td>

                @admin<td style="white-space: wrap !important"> {{ $profesor->created_at ?? null }}</td>@endadmin

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $profesor->id }}">
                    <div class="btn-group btn-group-sm">
                        @php $id_modal = 'modal_show_'.$profesor->id; @endphp
                        @include('administracion.configuraciones.profesors.modals.details')
                        <a title="Editar" class="btn btn-warning btn-sm"
                            href="{{route('administracion.configuraciones.profesors.edit',$profesor->id)}}" role="button">
                            <i class="{{ $icon_menus['editar'] ?? ''}} fa-1x"></i>
                        </a>
                        @php $disabled = ($profesor->status_delete) ? null:'disabled'; @endphp
                        <a title="Eliminar" class="btn-destroy btn btn-danger btn-sm {{$disabled ?? ''}}" href="#" id="btn-destroy_id_{{$profesor->id}}">
                            <i class="fas fa-trash"></i>
                        </a>
                    </div>
                </td>

            </tr>
            @endforeach

        </tbody>

    </table>

    {!! Form::open(['route' => ['administracion.configuraciones.profesors.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
    {!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
    {!! Form::close() !!}
    @section('scripts')
        @parent
        <script src="{{ asset("js/models/default/destroy.js") }}"></script>
    @endsection

    {{-- partials contentivo de los scripts datatables --}}
    @include('administracion.datatables.particulars.representans.exportBootstrap')
