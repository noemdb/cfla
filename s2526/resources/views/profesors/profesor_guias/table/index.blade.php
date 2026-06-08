@php
    $class_N="d-none d-sm-table-cell";
    $class_asignatura="";
    $class_action="nosort";
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_asignatura }}">P. Estudio</th>
            <th class="{{ $class_asignatura }}">Grado/Sección</th>
            <th class="{{ $class_asignatura }}">Estudiantes</th>
            <th class="{{ $class_action }}">F.Sabana</th>
            <th class="{{ $class_action }}">I.Diagnóstico</th>
        </tr>
    </thead>

    <tbody id="tdatos">

    @foreach($seccions as $seccion)

        @php
            $grado = $seccion->grado;
            $pestudio = $grado->pestudio;
            $lapso_name = $seccion->lapso_name;
        @endphp

        <tr data-grado_id="{{$grado->id}}" data-seccio-id="{{$seccion->id}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>

            <td class="{{ $class_asignatura  ?? ''}}">
                {{$pestudio->name ?? ''}}
            </td>

            <td class="{{ $class_asignatura  ?? ''}}">
                {{$grado->name ?? ''}} {{$seccion->name ?? ''}}
            </td>

            <td class="{{ $class_asignatura  ?? ''}}">
                {{$seccion->estudiants_in->count() ?? ''}}
            </td>

            <td class="{{ $class_action ?? '' }}">

                <div class="btn-group btn-group-sm">

                    <a id="btn_toprint" class="btn-toprint btn btn-dark" href="#" role="button" title="Generar PDF">
                        <i class="fa fa-file-pdf" aria-hidden="true"></i>
                    </a>

                </div>

            </td>

            <td class="{{ $class_action ?? '' }}">
                <div class="btn-group btn-group-sm">
                    @foreach ($diagMains as $item)
                        <a href="#"
                           class="btn-diag btn btn-info btn-sm"
                           data-diag-id="{{ $item->id }}"
                           data-seccion-id="{{ $seccion->id }}"
                           title="Ver Reporte Diagnóstico - {{ $item->name }}">
                            <i class="fas fa-chart-bar mr-1"></i>
                            {{-- <span class="d-none d-md-inline">{{ $item->name }}</span> --}}
                            <span class="d-inline">ID: {{ $item->id }}</span>
                        </a>
                    @endforeach
                </div>
            </td>

        </tr>

    @endforeach

    </tbody>

</table>

<div class="modal fade" id="modalDiag" tabindex="-1" role="dialog" aria-labelledby="modalDiagLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">

            <div class="modal-header bg-info text-white">
                <h5 class="modal-title" id="modalDiagLabel">
                    Informe Diagnóstico
                </h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>

            <div class="modal-body" id="modalDiagBody">
                <div class="text-center">
                    <i class="fas fa-spinner fa-spin"></i> Cargando...
                </div>
            </div>

        </div>
    </div>
</div>


{{-- partials contentivo de los scripts datatables --}}
@include('profesors.datatables.default')

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {

            $('.btn-toprint').click(function (e) {
                e.preventDefault();
                var row = $(this).parents('tr'); //fila contentiva de la data
                var grado_id = row.data('grado_id');  //console.log(estudiant_id);
                var seccio_id = row.data('seccio-id');  //console.log(estudiant_id);
                var url = '{{ route("profesors.profesor_guias.sabanafull.pdf",["_gid_","_sid_"]) }}';
                url = url.replace('_gid_', grado_id);
                url = url.replace('_sid_', seccio_id);
                window.open(url,'_blank');
            });

        });
    </script>
@endsection

@section('scripts')
    @parent
    <script>
    $(document).on('click', '.btn-diag', function (e) {
        e.preventDefault();

        let diagId = $(this).data('diag-id');
        let row = $(this).closest('tr');
        let seccionId = row.data('seccio-id');  // Asegúrate que este atributo existe
        
        // Construir la URL con ambos parámetros
        let url = "{{ route('profesors.profesor_guias.diag.show', ['diagMain' => ':diagId', 'seccion' => ':seccionId']) }}";
        url = url.replace(':diagId', diagId);
        url = url.replace(':seccionId', seccionId);

        // Mostrar loader y abrir modal
        $('#modalDiagBody').html(
            '<div class="text-center py-5">' +
            '   <div class="spinner-border text-primary" role="status">' +
            '       <span class="sr-only">Cargando...</span>' +
            '   </div>' +
            '   <p class="mt-2 text-muted">Cargando informe diagnóstico...</p>' +
            '</div>'
        );

        $('#modalDiag').modal('show');

        // Cargar el contenido del modal via AJAX
        $.ajax({
            url: url,
            method: 'GET',
            success: function(response) {
                $('#modalDiagBody').html(response);
            },
            error: function(xhr) {
                let errorMessage = 'Error al cargar el diagnóstico';
                if (xhr.status === 403) {
                    errorMessage = 'No tiene permisos para ver este diagnóstico';
                } else if (xhr.status === 404) {
                    errorMessage = 'Diagnóstico no encontrado';
                }
                
                $('#modalDiagBody').html(
                    '<div class="alert alert-danger text-center py-5">' +
                    '   <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>' +
                    '   <h5 class="font-weight-bold">' + errorMessage + '</h5>' +
                    '   <button class="btn btn-secondary mt-3" data-dismiss="modal">Cerrar</button>' +
                    '</div>'
                );
            }
        });
    });
    </script>
@endsection