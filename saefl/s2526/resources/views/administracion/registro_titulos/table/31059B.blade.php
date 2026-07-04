@php
    $class_N = 'd-none d-sm-table-cell';
    $class_profesor = '';
    $class_asignatura = '';
    $class_grado = '';
    $class_lapso = '';
    $class_action = 'nosort';
    $table_id = 'table-data-' . $pestudio->code_oficial . '-' . $registro_titulo->id;
    $form_id = 'form-aprove-' . $registro_titulo->id;
@endphp

{!! Form::open([
    'route' => ['administracion.titulos.aprove'],
    'method' => 'POST',
    'id' => $form_id,
    'role' => 'form',
]) !!}

{{ Form::hidden('registro_titulo_id', $registro_titulo->id) }}
<table width="100%" class="table table-striped table-hover table-sm small p-1" id="{{ $table_id ?? 'defualt' }}">

    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_profesor }}">Estudiante</th>
            <th class="{{ $class_profesor }} text-center">Sección</th>
            <th class="{{ $class_profesor }}">Serial del título</th>
            <th class="{{ $class_profesor }}">Observación</th>
            <th class="{{ $class_action }}">Acciones</th>
        </tr>
    </thead>

    <tbody id="tdatos">

        @php $n = null @endphp

        @foreach ($estudiants as $estudiant)
            @php
                $seccion = $estudiant->seccion;
                $titulo = $estudiant->getTitulo($registro_titulo->id);
            @endphp

            <tr data-estudiant_id="{{ $estudiant->id }}" class="{{ $titulo ? 'table-success' : 'table-default' }}">
                <td id="td-count" class="{{ $class_N }}">
                    {{ $loop->iteration }}
                </td>

                <td class="{{ $class_profesor ?? '' }}">
                    {{ $estudiant->fullname ?? '' }}
                    <br>
                    <small>
                        {{ $estudiant->ci_estudiant ?? '' }}
                    </small>
                    {{ Form::hidden('estudiant_id[' . $estudiant->id . ']', $estudiant->id) }}
                </td>

                <td class="{{ $class_profesor ?? '' }} text-center">
                    {{ $seccion->name ?? '' }}
                    {{ Form::hidden('seccion_id[' . $estudiant->id . ']', $seccion->id) }}
                </td>

                <td class="{{ $class_profesor ?? '' }}">
                    @php
                        $name = 'serie[' . $estudiant->id . ']';
                        $value = $titulo ? $titulo->serie : null;
                        // $value = '123' ;
                    @endphp
                    {!! Form::text($name, $value, [
                        'class' => 'form-control',
                        'placeholder' => $list_comment['serie'],
                        'id' => 'name',
                        'tabindex' => $n++,
                    ]) !!}
                </td>

                <td class="{{ $class_profesor ?? '' }}">
                    @php
                        $name = 'observations[' . $estudiant->id . ']';
                        $value = $titulo ? $titulo->observations : null;
                    @endphp
                    {!! Form::text($name, $value, [
                        'class' => 'form-control',
                        'placeholder' => $list_comment['observations'],
                        'id' => 'name',
                        'tabindex' => $n++,
                    ]) !!}
                </td>

                <td class="{{ $class_action ?? '' }}">

                    <div class="btn-group btn-group-sm">

                        @php
                            $name = 'aprove[' . $estudiant->id . ']';
                            $checked = $titulo ? 'checked' : null;
                        @endphp
                        <div class="input-group-text">
                            <input name="{{ $name }}" type="checkbox" class="aprove" {{ $checked ?? null }}
                                tabindex="{{ $n++ }}">
                        </div>

                        @php unset($status,$icon,$disabled) @endphp
                        @if (empty($estudiant->getTitulo($registro_titulo->id)))
                            @php
                                $status = 'success';
                                $icon = 'fa fa-check';
                                // $disabled = 'disabled';
                                $disabled = $titulo ? null : 'disabled';
                            @endphp
                        @endif

                        <a title="Imprimir Carta Culminación"
                            class="btn-print btn btn-dark btn-sm {{ $disabled ?? null }}"
                            href="{{ route('administracion.titulos.pdf.carta_culminacion', [$estudiant->id, $registro_titulo->id]) }}"
                            target="_blank" role="button">
                            <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                        </a>

                    </div>
                </td>

            </tr>
        @endforeach

    </tbody>

</table>

<div class="btn-group btn-block">

    <button type="submit" class="btn-boletin btn btn-primary w-75">
        <i class="fa fa-save" aria-hidden="true"></i>
        Guardar
    </button>

    <a name="" id="" class="btn btn-light w-25" href="{{ url()->full() }}" role="button"
        title="Refrescar la página'">
        <i class="fas fa-redo" aria-hidden="true"></i>
    </a>

</div>

{!! Form::close() !!}


{{-- partials contentivo de los scripts datatables --}}
@include('administracion.datatables.custom')
@section('scripts')
    @parent
    <script>
        // "pagingType": simple,simple_numbers,numbers,full,full_numbers,first_last_numbers
        $(document).ready(function() {
            $('#{{ $table_id ?? 'defualt' }}').DataTable({
                "pagingType": "simple_numbers",
                "pageLength": 10,
                "bLengthChange": false,
                "bPaginate": false,
                "searching": false,
                "bInfo": false,
                "responsive": false,
                "columnDefs": [{
                    "targets": 'nosort',
                    "orderable": false
                }],
                "language": {
                    "url": "/vendor/datatables/lang/spanish.json"
                }
            });
            $.fn.DataTable.ext.pager.numbers_length = 5;
        });
    </script>
@endsection


@section('scripts')
    <script type="text/javascript">
        $(document).ready(function() {
            $('.aprove').click(function(e) {
                var row = $(this).parents('tr'); //console.log(row); //fila contentiva de la data

                if (row.hasClass("table-success")) {
                    row.removeClass("table-success")
                    row.addClass("table-default")
                } else {
                    row.removeClass("table-default")
                    row.addClass("table-success")
                }
            });
        });
    </script>
@endsection
