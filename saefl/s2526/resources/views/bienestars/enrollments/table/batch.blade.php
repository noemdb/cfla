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
                <th class="{{ $class_estudiant }}">Nombres</th>
                <th class="{{ $class_estudiant }}">Apellidos</th>
                <th class="{{ $class_ci }}">Cédula</th>
                <th class="{{ $class_grado }}">Grado/Sección</th>
                <th class="{{ $class_action }}">Acción</th>
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
                        {{$estudiant->name}}
                    </td>

                    <td id="td-estudiant-{{ $estudiant->id }}" class="{{ $class_estudiant  ?? ''}}">
                        {{$estudiant->lastname}}
                    </td>

                    <td id="td-profiles-ci_estudiant-{{ $estudiant->id }}" class="{{ $class_ci ?? '' }}">
                        <a class="btn-link" href="{{ route('administracion.estudiants.index',['search'=>$estudiant->ci_estudiant]) }}">
                            <span class="font-weight-bold text-{{ ($ammount_expire_bill>0) ? 'danger':'dark'}}">
                                {{$estudiant->ci_estudiant}}
                            </span>
                        </a>
                    </td>

                    <td id="td-users-is_active-{{ $estudiant->id }}" class="{{ $class_grado ?? '' }}">
                        <span class="{{$estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default'}}">
                            {{$estudiant->getInscripcion()->seccion->grado->name ?? ''}} {{$estudiant->getInscripcion()->seccion->name ?? ''}}
                        </span>
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="btn-group btn-group-sm">

                            <a title="Imprimir ficha del estudiante" class="btn btn-secondary bnt-sm" href="{{ route('bienestars.student_records.pdf.ficha.estudiant',$estudiant->id) }}" role="button" target="_BLANK">
                                <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                            </a>

                        </div>

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    <div id="container_modal"></div>

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
