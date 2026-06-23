@php
    $class_N = 'd-none d-sm-table-cell';
    $class_estudiant = '';
    $class_ci = '';
    $class_pensum = 'nosort';
    $class_action = 'nosort';
@endphp

<table width="100%" class="table table-striped table-hover table-sm p-1" id="table-data-default">

    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_estudiant }}">Identificador</th>
            <th class="{{ $class_estudiant }}">Estudiante</th>
            @if (!empty($pevaluacion))
                @if (!empty($pevaluacion->evaluacions->first()))
                    @foreach ($pevaluacion->evaluacions as $evaluacion)
                        <th class="{{ $class_pensum }} text-center" title="{{ $evaluacion->description ?? '' }}">
                            {{ $loop->iteration }}
                        </th>
                    @endforeach
                @else
                    <th class="alert alert-danger text-center">NO HAY EVALUACIONES REGISTRADAS</th>
                @endif
            @else
                <th class="alert alert-danger text-center">NO HAY PLAN DE EVALUACIÓN REGISTRADO</th>
            @endif

            <th class="{{ $class_action ?? '' }}">Acción</th>

            <th class="{{ $class_estudiant ?? '' }}">Promedio</th>

        </tr>
    </thead>

    <tbody id="tdatos">
        @foreach ($estudiants as $estudiant)

            <tr data-id="{{ $estudiant->id }}">
                <td id="td-count" class="{{ $class_N }}">
                    {{ $loop->iteration }}
                </td>
                <td class="{{ $class_estudiant }}">
                    {{ $estudiant->ci_estudiant ?? '' }}
                </td>
                <td class="{{ $class_estudiant }}">
                    {{ $estudiant->fullname }}
                </td>

                {!! Form::open([
                    'route' => 'directors.boletins.store',
                    'method' => 'POST',
                    'id' => $estudiant->id,
                    'class' => 'form-nota pb-2',
                    'role' => 'form-signin',
                ]) !!}

                @csrf

                {{ Form::hidden('estudiant_id', $estudiant->id, ['id' => 'estudiant_id']) }}
                {{ Form::hidden('grado_id', $grado->id, ['id' => 'grado_id']) }}
                {{ Form::hidden('lapso_id', $lapso->id, ['id' => 'lapso_id']) }}
                {{ Form::hidden('seccion_id', $seccion->id, ['id' => 'seccion_id']) }}
                {{ Form::hidden('pensum_id', $pensum->id, ['id' => 'pensum_id']) }}



                @if ($pevaluacion)
                    @php $studiant_current = $estudiant; @endphp

                    @if (!empty($pevaluacion->evaluacions->first()))
                        @php $acum_nota = 0; @endphp
                        @php $count_eva = 0; @endphp
                        @foreach ($pevaluacion->evaluacions as $evaluacion)
                            <td class="{{ $class_pensum }} text-center">
                                @php
                                    $name = 'nota[' . $estudiant->id . '][' . $evaluacion->id . ']';
                                    $minimo = 0;
                                    // $minimo = $evaluacion->escala->minimo;
                                    $maximo = $evaluacion->escala->maximo;
                                    $nota = !empty(
                                        $evaluacion->boletins->where('estudiant_id', $estudiant->id)->first()->id
                                    )
                                        ? $evaluacion->boletins->where('estudiant_id', $estudiant->id)->first()->nota
                                        : null;
                                @endphp
                                {{-- {!! Form::selectRange($name, $minimo, $maximo,$nota,['class'=>'obj-select-'.$estudiant->id,'placeholder' => '']) !!} --}}
                                {!! Form::select($name, $list_nota ?? [], $nota, [
                                    'class' => 'obj-select-' . $estudiant->id,
                                    'id' => $name,
                                    'placeholder' => '',
                                ]) !!}
                            </td>
                            @if ($nota)
                                @php $count_eva = $count_eva + 1; @endphp
                                @php $acum_nota = $acum_nota + $nota; @endphp
                            @endif
                        @endforeach
                    @else
                        <td></td>
                    @endif
                @else
                    <td></td>
                @endif

                <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">
                    @if (!empty($pevaluacion->evaluacions->first()))
                        <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

                            <button type="submit" class="btn-boletin btn btn-outline-primary"
                                {{ $pevaluacion->id ?? 'disabled' }}
                                value="btn-create-boletin_{{ $estudiant->id ?? '' }}"
                                id="btn-create-boletin_{{ $estudiant->id ?? '' }}">
                                <i class="fa fa-save" aria-hidden="true"></i>
                            </button>

                        </div>
                    @endif
                </td>
                {!! Form::close() !!}

                <td>
                    <span id="promedio_{{ $estudiant->id ?? '' }}">
                        {{ !empty($count_eva) ? round($acum_nota / $count_eva, 2) : '' }}
                    </span>
                </td>

            </tr>

        @endforeach
    </tbody>

    <caption>
        <button id="all_sumit" class="btn btn-primary w-100 btn-sm" type="submit">
            <i class="fa fa-save" aria-hidden="true"></i>
            Guargar
        </button>
    </caption>

</table>

@section('stylesheet')
    @parent
    <link rel="stylesheet" href="{{ asset('vendor/datatables/DataTables-1.10.16/css/dataTables.bootstrap4.css') }}">
@endsection

@section('scripts')
    @parent
    <script src="{{ asset('vendor/datatables/DataTables-1.10.16/js/jquery.dataTables.js') }}"></script>
    <script src="{{ asset('vendor/datatables/DataTables-1.10.16/js/dataTables.bootstrap4.js') }}"></script>
    <script src="{{ asset('js/models/datatable/simple.js') }}"></script>
@endsection

@section('scripts')
    @parent

    <script type="text/javascript">
        //ini del evento clic
        $('.btn-boletin').click(function(e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id'); //console.log(id);
            var form = $('#' + id); //console.log(form.attr('action'));
            var data = form.serialize(); //console.log(data); console.log(data);
            var url = "{{ route('directors.boletins.store') }}"; //console.log(url);
            $.post(url, data, function(result) {
                $('.obj-select-' + id).attr('disabled', 'disabled');
                $('#promedio_' + id).text(result.promedio);
            }).fail(function(result) {
                Swal.fire({
                    title: 'ERROR',
                    type: 'error'
                });
            });
        });
        //fin del evento clic
    </script>
@endsection

@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            // sumit todos los formularios
            $('#all_sumit').click(function(e) {
                e.preventDefault(); //console.log('123');

                $(".form-nota").each(function() {
                    var form = $(this); //console.log(form.attr('action'));;
                    var id = form.attr('id'); //console.log(id);
                    var data = form.serialize(); //console.log(data); //console.log(data);
                    var url = "{{ route('directors.boletins.store') }}"; //console.log(url);

                    $.post(url, data, function(result) {
                        $('#promedio_' + id).text(result.promedio)
                    }).fail(function(result) {
                        // Swal.fire({
                        //         title: 'ERROR',
                        //         type: 'error'
                        //     });
                    });
                    $('.obj-select-' + id).attr('disabled', 'disabled');
                    $('#all_sumit').attr('disabled', 'disabled');

                });
                Swal.fire({
                    title: 'Registros Guardados',
                    type: 'success'
                });
            });
        });
    </script>
@endsection
