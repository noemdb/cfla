{{-- <h6 class="pb-2 font-weight-bold ">Listado</h6> --}}
<hr>
@php
    $class_N="d-none d-sm-table-cell";
    $class_profesor="";
    $class_asignatura="";
    $class_grado="";
    $class_lapso="";
    $class_action="nosort text-center";
@endphp

<table width="100%" class="table table-striped table-sm table-hover p-1 small" id="table-data-default">
    {{-- <caption style="caption-side: top-right">Listado</caption> --}}
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_profesor }}">Profesor</th>
            <th class="{{ $class_asignatura }}">Asignatura</th>
            <th class="{{ $class_asignatura }}">Grado/Sección</th>
            <th class="{{ $class_asignatura }}">Lapso</th>
            <th class="{{ $class_lapso }}">Evaluaciones</th>
            <th class="{{ $class_action }}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">
    @foreach($pevaluacions as $pevaluacion)

        @php $pensum = $pevaluacion->pensum; $grado = $pevaluacion->pensum->grado; $pensum = $pevaluacion->pensum; @endphp

        <tr data-id="{{$pevaluacion->id}}" data-pevaluacion="{{$pevaluacion->id ?? ''}}"
        class="table-{{(empty($pevaluacion->administrativa->id)) ? 'default':'success'}}">
            <td id="td-count" class="{{ $class_N }}">
                {{$loop->iteration}}
            </td>

            <td id="td-pevaluacion-profesor-{{ $pevaluacion->id }}" class="{{ $class_user  ?? ''}}" title="{{$pevaluacion->profesor->fullname ?? ''}}">
                {{ Str::limit($pevaluacion->profesor->fullname,20,'...') ?? ''}}
            </td>

            <td class="{{ $class_asignatura ?? '' }}" title="{{$pevaluacion->pensum->asignatura->name ?? '' }}">
                {{ $pevaluacion->pensum->asignatura->code ?? ''}}
            </td>

            <td class="{{ $class_asignatura ?? '' }}">
                {{ $pevaluacion->pensum->grado->name ?? ''}}
                {{ $pevaluacion->seccion->name ?? ''}}
            </td>
            <td class="{{ $class_asignatura ?? '' }}">
                {{ $pevaluacion->lapso->name ?? ''}}
            </td>

            <td id="td-boletins-{{ $pevaluacion->id }}" class="{{ $class_lapso ?? '' }}">
                {{ $pevaluacion->evaluacions->count() ?? ''}}
            </td>

            <td class="{{ $class_action ?? '' }}" >
                <div class="btn-group">

                    @php $class_btn = ($pevaluacion->status_eva_complete) ? 'btn-info' : 'btn-outline-info' ; @endphp
                    <a title="Registrar Evaluaciones" class="btn {{$class_btn ?? ''}}" href="{{route('administracion.evaluacions.create',$pevaluacion->id)}}" role="button">
                        <i class="{{ $icon_menus['evaluacion'] ?? ''}} fa-1x"></i>
                    </a>

                    {{-- @php $disabled  = ($pevaluacion->evaluacions->IsEmpty()) ? ' disabled ': null ; @endphp
                    @php $class_btn = ($pevaluacion->evaluacions->IsEmpty()) ? 'btn-outline-secondary' : 'btn-primary' ; @endphp
                    <a title="Registro de Notas" class="btn {{$class_btn ?? ''}} {{$disabled ?? ''}} btn-primary" href="{{route('administracion.boletins.carga',['pevaluacion_id'=>$pevaluacion->id])}}" role="button">
                        <i class="{{ $icon_menus['notas'] ?? ''}} fa-1x"></i>
                    </a> --}}

                </div>
            </td>

            {{-- <td class="{{ $class_action ?? '' }}" >
                <div class="btn-group" role="group" aria-label="Basic example">
                    @php $id_container = 'container_modal_'.$pevaluacion->id; @endphp
                    <a title="Registrar Evaluaciones" class="btn-modal btn btn-info btn-sm" href="#">
                        <i class="{{ $icon_menus['show'] ?? '' }} fa-1x"></i>
                    </a>
                    <div id="{{$id_container ?? ''}}"></div>

                    @php $modal_id = 'modal_'.$pevaluacion->id; @endphp
                    @php $class_btn = ($pevaluacion->status_eva_complete) ? 'btn-primary' : 'btn-outline-primary' ; @endphp

                    <a title="Registrar Evaluaciones"  data-id="{{$pevaluacion->id ?? ''}}" class="btn btn-sm {{$class_btn ?? ''}}" href="#" data-target="#{{$modal_id ?? ''}}" data-toggle="modal">
                        <i class="{{ $icon_menus['evaluacion'] ?? ''}} fa-1x"></i>
                    </a>

                    @component('administracion.elements.widgets.modal')
                        @slot('modal_id',$modal_id)
                        @slot('body')
                            @include('administracion.pevaluacions.evaluacions.partials.modal.create')
                        @endslot
                        @slot('title','Plan de evaluación')
                        @slot('classH','secondary')
                        @slot('size','modal-xl')
                    @endcomponent
                </div>

            </td> --}}
        </tr>
        @endforeach

    </tbody>

</table>

{{-- @section('scripts')
    @parent
    <script>
        $('.btn-modal').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  //console.log(id);
            var modal = '#modal_registropago_'+id;  //console.log(modal);
            var container = '#container_modal_'+id;  //console.log(container);
            var ajaxurl = '{{route("administracion.ajax.fill.modal.registro_pago_combinado", "_id_")}}';
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
@endsection --}}

@section('scripts')
    @parent
    <script type="text/javascript">
        //ini del evento clic
        $('.btn-evaluacion-create').click(function (e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');  console.log(id);
            var form = $('#form-evaluacions-create_'+id); //console.log(form.attr('action'));
            var data = form.serialize(); console.log(data); //console.log(data);
            // var url = "{{ route('administracion.evaluacions.store') }}"; console.log(url);
            var url = form.attr('action'); console.log(url);
            $.post(url, data, function (result){
                Swal.fire({
                    title: result.messenge,
                    icon: 'success'
                });
            }).fail(function (result) {
                Swal.fire({
                        title: 'ERROR',
                        type: 'error'
                    });
            });
        });
        //fin del evento clic

        $('.modal-obj').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); //console.log(button); // Button that triggered the modal
            var recipient = button.data('id'); //console.log(recipient); // Extract info from data-* attributes
            // var form = $('#form-evaluacions-create_'+recipient); //console.log(form.attr('action'));
            // var data = form.serialize(); console.log(data); //console.log(data);
            // If necessary, you could initiate an AJAX request here (and then do the updating in a callback).
            // Update the modal's content. We'll use jQuery here, but you could use a data binding library or other methods instead.
            var modal = $(this); //console.log(modal);
            // modal.find('.modal-title').text('New message to ' + recipient);
            // modal.find('.modal-body input').val(recipient);
        })
    </script>
@endsection

{!! Form::open(['route' => ['administracion.pevaluacions.destroy',':PEVALUCION_ID'], 'method' => 'DELETE', 'id'=>'form-destroy', 'role'=>'form']) !!}
{!! Form::close() !!}
@section('scripts') @parent <script src="{{ asset("js/models/pevaluacions/destroy.js") }}"></script> @endsection

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/1.10.20/datatables/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/jquery.dataTables.js") }}"></script>
    <script src="{{ asset("vendor/datatables/1.10.20/datatables/js/dataTables.bootstrap4.js") }}"></script>
    <script src="{{ asset("js/models/datatable/simple.js") }}"></script>
@endsection


