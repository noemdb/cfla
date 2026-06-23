@php
    $class_N="d-none d-sm-table-cell";
    $class_estudiant="";
    $class_ci="d-none d-md-table-cell";
    $class_planpago="d-none d-lg-table-cell text-nowrap";
    $class_deuda="d-none d-lg-table-cell text-nowrap";
    $class_grado="d-none d-lg-table-cell";
    $class_fecha="text-nowrap";
    $class_action="";
@endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_ci }}">Cédula</th>
                @admon
                <th class="{{ $class_planpago }}">Plan de Pago</th>
                @endadmon
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_action }}">GSEmail</th>
                @admin<th class="{{ $class_action }}">N. Usuario</th>@endadmin
                {{-- <th class="{{ $class_action }}">Acción</th> --}}
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($estudiants as $estudiant)
                @php
                    $ammount_expire_bill = 0;
                    $status_active = ($estudiant->status_active=='true') ? true : false;
                @endphp
                <tr data-estudiant_id="{{$estudiant->id ?? ''}}" data-id="{{$estudiant->id ?? ''}}" class="{{($status_active) ? '':'table->danger'}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td id="td-estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant  ?? ''}}">
                        <a class="btn-link" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}">
                            <span class="font-weight-bold text-{{ ($ammount_expire_bill>0) ? 'danger':'dark'}}">
                                {{$estudiant->fullname}}
                            </span>
                        </a>
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_ci ?? '' }}">
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </td>

                    @admon
                    <td id="td-planpago-estudiant-{{ $estudiant->id }}" class="{{ $class_planpago ?? '' }}">
                        @if (empty($estudiant->administrativa->planpago_id))
                            <span class="badge badge-secondary mt-1" title="SIN PLAN DE PAGO ASIGNADO">.NINGUNO.</span>
                        @else
                            {!!$estudiant->administrativa->planpago->badge ?? ''!!}
                        @endif
                    </td>
                    @endadmon

                    <td id="td-users-is_active-{{ $estudiant->id }}" class="{{ $class_grado ?? '' }}">
                        <span class="{{$estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default'}}">
                            {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}} {{$estudiant->getInscripcion()->seccion->name ?? ''}}
                        </span>
                    </td>
                    {{-- <td class="{{ $class_grado ?? '' }}">
                        <span class=" font-weight-bold text-{{ ($status_active) ? 'success':'danger'}}">
                            {{ ($status_active) ? 'SI':'NO'}}
                        </span>
                    </td> --}}
                    <td style="white-space: wrap !important">
                        {{ $estudiant->gsemail ?? ''}}</span>
                    </td>
                    @admin<td style="white-space: wrap !important"> {{ ($estudiant->user) ? $estudiant->user->username : null }}</td>@endadmin

                    {{-- <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="btn-group btn-group-sm">

                            <a title="Resumen" class="btn-card btn btn-info bnt-sm" href="#">
                                <i class="{{ $icon_menus['profile'] }} fa-1x"></i>
                            </a>
                            <a title="Editar datos del estudiante" class="btn btn-warning bnt-sm" href="{{ route('administracion.estudiants.edit',['id'=>$estudiant->id]) }}" role="button">
                                <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                            </a>

                        </div>

                    </td> --}}
                </tr>
            @endforeach

        </tbody>
    </table>

    <div id="container_modal"></div>

{!! Form::open(['route' => ['administracion.estudiants.destroy',':ESTUDIANT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/estudiants/destroy.js") }}"></script> @endsection

{{-- @include('administracion.datatables.default') --}}
@include('administracion.datatables.exportBootstrap')

@section('scripts')
    @parent
    <script>
        $('.btn-card').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var modal = '#modal_card';  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.estudiant_card", "_id_")}}';
            ajaxurl = ajaxurl.replace('_id_', id);
            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data){
                    $(container).html(data);
                    $(modal).modal('toggle');
                }
            });
        });
    </script>
@endsection
