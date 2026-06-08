@php
    $class_N="d-none d-sm-table-cell";
    $class_asignatura="";
    $class_code_sm="d-none d-lg-table-cell";
    $class_ht="d-none d-md-table-cell";
    $class_hp="d-none d-lg-table-cell";
    $class_action="";
    $class_action="nosort";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_asignatura }}">Ident.</th>
            <th class="{{ $class_asignatura }}">Estudiante</th>
            <th class="{{ $class_asignatura }}">Grado/Sección</th>
            <th class="{{ $class_code_sm }}">Lapsos/Cantidad</th>
            {{-- <th class="{{ $class_code_sm }}">Promedio</th> --}}
            <th class="{{ $class_action }}">E. Descriptiva</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($estudiants as $estudiant)

        <tr data-id="{{$estudiant->id}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>
            <td class="{{ $class_asignatura  ?? ''}}">
                {{$estudiant->ci_estudiant ?? ''}}
            </td>
            <td class="{{ $class_asignatura  ?? ''}}">
                {{$estudiant->fullname ?? ''}}
            </td>

            <td class="{{ $class_asignatura  ?? ''}}">
                {{$estudiant->fullinscripcion ?? ''}}
            </td>

            <td class="{{ $class_code_sm  ?? ''}}">

                @php $edescriptivas = (!empty($estudiant->edescriptivas)) ? $estudiant->edescriptivas->sortBy('lapso_id') : collect() ; @endphp
                <span class="badge badge-light float-right">
                    <span id="span_count_{{$estudiant->id}}">
                        {{ $edescriptivas->count() ?? null }}
                    </span>
                </span>
                <div class="btn-group btn-group-sm" role="group" aria-label="">
                    @foreach ($edescriptivas as $edescriptiva)
                        @php $elapso = (!empty($edescriptiva->lapso)) ? $edescriptiva->lapso : null @endphp
                        <a class="btn btn-{{ ($elapso) ? $elapso->color: 'dark' }}" href="#" role="button">{{ ($elapso) ? $elapso->id : 'F' }}</a>
                    @endforeach
                </div>

            </td>

            {{-- <td class="{{ $class_code_sm  ?? ''}}"> --}}
                {{-- {{ $estudiant->promedio ?? ''}} --}}
            {{-- </td> --}}

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <a title="Detalles de las evaluaciones descriptivas" class="btn-modal-details btn btn-info btn-sm" href="#" role="button">
                        <i class="{{ $icon_menus['show'] ?? ''}} fa-1x"></i>
                    </a>

                    <a title="Registrar/Actualizar" class="btn-modal-create btn btn-warning btn-sm" href="#" role="button">
                        <i class="{{ $icon_menus['nuevo'] ?? ''}} fa-1x"></i>
                    </a>

                    <a id="btn_toprint" class="btn-toprint btn btn-light" href="#" role="button" title="Generar PDF">
                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
                    </a>

                </div>
            </td>
        </tr>
    @endforeach

    </tbody>

</table>

<div id="container_modal"></div>

{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.default')

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {

            $('.btn-toprint').click(function (e) {
                e.preventDefault();
                var row = $(this).parents('tr'); //fila contentiva de la data
                var id = row.data('id');  //console.log(estudiant_id);
                var url = '{{ route("administracion.edescriptivas.edescriptiva.pdf",["_id_"]) }}';
                url = url.replace('_id_', id);
                window.open(url,'_blank');
            });

            $('.btn-modal-create').click(function (e) {
                e.preventDefault();
                var row = $(this).parents('tr'); //fila contentiva de la data
                var id = row.data('id');  //console.log(id);
                var modal = '#modal_create_edescriptivas_'+id;  //console.log(container);
                var container = '#container_modal';  //console.log(container);
                var ajaxurl = '{{route("administracion.ajax.fill.modal.edescriptiva.create", "_id_")}}';
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

            $('.btn-modal-details').click(function (e) {
                e.preventDefault();
                var row = $(this).parents('tr'); //fila contentiva de la data
                var id = row.data('id');  //console.log(id);
                var modal = '#modal_details_edescriptivas_'+id;  //console.log(container);
                var container = '#container_modal';  //console.log(container);
                var ajaxurl = '{{route("administracion.ajax.fill.modal.edescriptiva.details", "_id_")}}';
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

        });
    </script>
@endsection

