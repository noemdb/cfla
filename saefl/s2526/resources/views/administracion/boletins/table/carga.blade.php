@php
    $class_N = 'd-none d-sm-table-cell';
    $class_estudiant = '';
    $class_ci = '';
    $class_pensum = 'nosort';
    $class_action = 'nosort';
@endphp

{!! Form::open([
    'route' => 'administracion.boletins.store',
    'method' => 'POST',
    'id' => 'form-nota',
    'class' => 'form-nota pb-2',
    'role' => 'form-signin',
]) !!}

<fieldset id="fieldset">

    {{ Form::hidden('grado_id', $grado->id, ['id' => 'grado_id']) }}
    {{ Form::hidden('lapso_id', $lapso->id, ['id' => 'lapso_id']) }}
    {{ Form::hidden('seccion_id', $seccion->id, ['id' => 'seccion_id']) }}
    {{ Form::hidden('pensum_id', $pensum->id, ['id' => 'pensum_id']) }}

    <table width="100%" class="table table-striped table-hover table-sm p-1 small" id="table-data-default">
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
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach ($estudiants as $estudiant)

                <tr data-id="{{ $estudiant->id }}"
                    class="table-{{ $estudiant->id == $boletin->estudiant_id ? 'info' : 'default' }}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{ $loop->iteration }}
                    </td>
                    <td id="td-users-username-{{ $estudiant->id }}" class="{{ $class_user ?? '' }}">
                        {{ $estudiant->ci_estudiant ?? '' }}
                    </td>
                    <td id="td-users-username-{{ $estudiant->id }}" class="{{ $class_user ?? '' }}">
                        <a class="btn-link text-dark small"
                            href="{{ route('administracion.estudiants.index', ['search' => $estudiant->ci_estudiant]) }}">
                            {{ $estudiant->fullname }}<br>
                        </a>
                    </td>

                    @if ($pevaluacion)
                        @php $studiant_current = $estudiant; @endphp

                        @if (!empty($pevaluacion->evaluacions->first()))
                            @foreach ($pevaluacion->evaluacions as $evaluacion)
                                @php
                                    $name = 'nota[' . $estudiant->id . '][' . $evaluacion->id . ']';
                                    $minimo = 0;
                                    $maximo = $evaluacion->escala->maximo;
                                    $nota = !empty(
                                        $evaluacion->boletins->where('estudiant_id', $estudiant->id)->first()->id
                                    )
                                        ? $evaluacion->boletins->where('estudiant_id', $estudiant->id)->first()->nota
                                        : null;
                                    $readonly = Auth::user()->isControlDir() ? null : ($nota ? ' readonly ' : null);
                                    $disabled = Auth::user()->isControlDir() ? null : ($nota ? ' disabled ' : null);
                                @endphp
                                <td
                                    class="td-nota td-nota{{ $estudiant->id ?? '' }} {{ $class_pensum }} text-center table-{{ $nota ? 'light' : 'danger' }}">
                                    <fieldset {{ $disabled ?? null }}>
                                        {!! Form::select($name, $list_nota, $nota, [
                                            'class' => 'obj-select-' . $estudiant->id . $readonly,
                                            'id' => $name,
                                            'tabindex' => $loop->iteration,
                                        ]) !!}
                                    </fieldset>
                                </td>
                            @endforeach
                        @else
                            <td>&nbsp;</td>
                        @endif
                    @else
                        <td>&nbsp;</td>
                    @endif

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        @if (!empty($pevaluacion->evaluacions->first()))
                            <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">

                                <button type="button" class="btn-boletin btn btn-outline-primary">
                                    <i class="fa fa-save" aria-hidden="true"></i>
                                </button>

                                <a target="_blank" class="btn btn-outline-info"
                                    href="{{ $route = route('administracion.boletins.boletin.pdf', [$estudiant->id, $pevaluacion->lapso->id]) }}"
                                    role="button">
                                    <i class="{{ $icon_menus['boletin'] ?? '' }} "></i>
                                </a>

                            </div>
                        @endif

                    </td>
                </tr>

            @endforeach

        </tbody>

    </table>

</fieldset>

<div class="btn-group btn-block">

    <button type="submit" class="btn-boletin btn btn-primary w-75 {{ $readonly ? 'disabled' : null }}">
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
@include('administracion.datatables.simple')

@section('scripts')
    @parent

    <script type="text/javascript">
        //ini del evento clic
        $('.btn-boletin').click(function(e) {
            e.preventDefault();
            var row = $(this).parents('tr'); //fila contentiva de la data
            var id = row.data('id');
            console.log(id);
            var form = $('#form-nota'); //console.log(form.attr('action'));
            var data = form.serialize(); //console.log(data);
            var url = "{{ route('administracion.boletins.store') }}"; //console.log(url);
            $.post(url, data, function(result) {
                if (id) {
                    $('.obj-select-' + id).attr('disabled', 'disabled');
                    $('.td-nota' + id).removeClass('table-danger');
                    Swal.fire({
                        titleText: 'Resultado',
                        html: '<h5>' + result.messenge + '</h5>',
                        showConfirmButton: false,
                        icon: 'success',
                        timer: 2000,
                        timerProgressBar: true
                    });
                } else {
                    $('#fieldset').attr('disabled', 'disabled');
                    $('.td-nota').removeClass('table-danger');
                    Swal.fire({
                        titleText: 'Resultado',
                        html: '<h5>' + result.messenge + '</h5>',
                        icon: 'success'
                    });
                }

            }).fail(function(result) {
                Swal.fire({
                    title: 'ERROR',
                    icon: 'error'
                });
            });
        });
        //fin del evento clic
    </script>
@endsection
