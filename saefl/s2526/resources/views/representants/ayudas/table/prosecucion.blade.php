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
                <th class="{{ $class_ci }}">Grado</th>
                <th class="{{ $class_ci }}">Sección</th>
                <th class="{{ $class_ci }}">Tipo</th>
                <th class="{{ $class_ci }}">Definitiva</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach($inscripcions as $inscripcion)

                @php $estudiant = $inscripcion->estudiant; @endphp

                <tr data-id="{{$inscripcion->id ?? ''}}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{$loop->iteration}}
                    </td>

                    <td class="{{ $class_estudiant  ?? ''}}">
                        @include('representants.estudiants.partials.href')
                    </td>

                    <td class="{{ $class_ci ?? '' }}">
                        {{ $estudiant->ci_estudiant ?? ''}}
                    </td>
                    <td class="{{ $class_ci ?? '' }}">
                        {{ $estudiant->grado->name ?? ''}}
                    </td>

                    <td class="{{ $class_ci ?? '' }}">
                        {{ $estudiant->seccion->name ?? ''}}
                    </td>

                    <td class="{{ $class_ci ?? '' }}">
                        {{ $inscripcion->tinscripcion->name ?? ''}}
                    </td>

                    <td class="{{ $class_ci ?? '' }}">
                        {{ $estudiant->literal ?? null }}
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="btn-group btn-group-sm">
                            <a class="btn btn-primary" href="{{route('representants.ayudas.constancia.prosecucion.pdf',$estudiant->id)}}" target="_blank" role="button">
                                <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x text-light"></i>
                            </a>
                        </div>

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>
    {!! Form::open(['route' => ['representants.ayudas.destroy',':IDENT_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
    {!! Form::hidden('name_id', ':IDENT_ID',['id'=>'name_id']) !!}
    {!! Form::close() !!}
    @section('scripts')  @parent <script src="{{ asset("js/models/default/destroy.js") }}"></script> @endsection

    <div id="container_modal"></div>

{{-- partials contentivo de los scripts datatables --}}
@include('representants.datatables.default')

<div id="container_modal"></div>

@section('scripts')
    @parent
    <script>
        $('.btn-ge').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var modal = '#modal_update_'+id;  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.inscripcions.grupo_estable.update", "_id_")}}';
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
