@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_asignatura }}">Asignatura</th>
            <th class="{{ $class_asignatura }}">P. Estudio</th>
            <th class="{{ $class_asignatura }}">Grado</th>
            <th class="{{ $class_asignatura }}">Sección</th>
            <th class="{{ $class_action }}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">

    @foreach($pevaluacions as $pevaluacion)

        @php
            $pensum = $pevaluacion->pensum;
            $asignatura = $pensum->asignatura;
            $grado = $pensum->grado;
            $pestudio = $grado->pestudio;
            $seccion = $pevaluacion->seccion;
        @endphp

        <tr data-seccion_id="{{$seccion->id}}" data-pensum_id="{{$pensum->id}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>

            <td class="{{ $class_asignatura  ?? ''}}">
                {{$asignatura->name ?? ''}}
            </td>
            <td class="{{ $class_asignatura  ?? ''}}">
                {{$pestudio->name ?? ''}}
            </td>

            <td class="{{ $class_asignatura  ?? ''}}">
                {{$grado->name ?? ''}}
            </td>

            <td class="{{ $class_asignatura  ?? ''}}">
                {{$seccion->name ?? ''}}
            </td>

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <a title="Mostrar detalles del registro de pago" class="btn-modal btn btn-info btn-sm" href="#">
                        <i class="fas fa-info"></i>
                    </a>

                    <a title="Planilla de Registro de Notas" target="_blank" class="btn btn-dark" href="{{route('profesors.boletins.sabana_single.pdf',['pensum_id'=>$pensum->id,'seccion_id'=>$seccion->id])}}" role="button">
                        <i class="{{ $icon_menus['pdf'] ?? ''}} fa-1x"></i>
                    </a>

                </div>

            </td>

        </tr>

    @endforeach

    </tbody>

</table>

<div id="container_modal"></div>

@include('profesors.boletins.show.modal.spinner')

{{-- partials contentivo de los scripts datatables --}}
{{-- @include('profesors.datatables.default') --}}

@section('scripts')
    @parent
    <script>

        $('.btn-modal').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var seccion_id = row.data('seccion_id');  //console.log(id);
            var pensum_id = row.data('pensum_id');  //console.log(id);
            var modal = '#modal_sabanafull_seccion_'+seccion_id;  //console.log(modal);
            var container = '#container_modal';  //console.log(container);
            var ajaxurl = '{{route("profesors.ajax.fill.modal.boletins.sabanafull.show", ["seccion_id"=>"_seccion_id_", "pensum_id"=>"_pensum_id_"])}}';
            $("#modal_spinner").modal('toggle');
            ajaxurl = ajaxurl.replace('_seccion_id_', seccion_id); ajaxurl = ajaxurl.replace('_pensum_id_', pensum_id);
            $.ajax({
                url: ajaxurl,
                type: "GET",
                success: function(data){
                    $(container).html(data);
                    $("#modal_spinner").modal('toggle');
                    $(modal).modal('toggle');
                }
            });
        });

        //****************************************************************************************************************************

    </script>
@endsection
