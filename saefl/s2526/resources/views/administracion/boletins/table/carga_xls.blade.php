@php
    $class_N = 'd-none d-sm-table-cell';
    $class_estudiant = '';
    $class_ci = '';
    $class_pensum = 'nosort';
    $class_action = 'nosort';
@endphp

<fieldset id="fieldset">

    <table width="100%" class="table table-striped table-hover table-sm p-1 small" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Identificador</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_estudiant }}" title="Notas del archivo">Nota XLS</th>
                <th class="{{ $class_estudiant }} text-center">
                    Evaluaciones/Notas
                    <ul class="list-group list-group-horizontal px-2">
                        @foreach ($evaluacions as $evaluacion)
                            @php
                                $fecha_aplicacion = !empty($evaluacion->fecha) ? f_date($evaluacion->fecha) : null;
                                $title = $fecha_aplicacion . ' - ' . $evaluacion->description;
                            @endphp
                            <li class="list-group-item p-1 text-center font-weight-bold" title="{{ $title ?? '' }}"
                                style="width: 2rem !important">
                                {{ $loop->iteration ?? '' }}
                            </li>
                        @endforeach
                    </ul>
                </th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach ($estudiants as $estudiant)
                @php
                    $name = 'nota[' . $estudiant->id . ']';
                    $value = isset($notasXlsCol[$estudiant->ci_estudiant])
                        ? $notasXlsCol[$estudiant->ci_estudiant]
                        : null;
                    $data = collect();
                    $data->put('id', $estudiant->id);
                    $data->put('ci_estudiant', $estudiant->ci_estudiant);
                    $data->put('fullname', $estudiant->fullname);
                    $data->put('nota', $value);
                @endphp

                @empty($value)
                    @php $ci_not_founds->put($estudiant->id,$data); @endphp
                @endempty

                @if (isset($value) && ($value < $minimo || $value > $maximo))
                    @php $nota_out_ranges->put($estudiant->id,$data); @endphp
                @endif

                <tr data-id="{{ $estudiant->id }}"
                    class="{{ $value ? null : 'table-danger' }} {{ isset($value) && ($value < $minimo || $value > $maximo) ? 'table-warning' : null }}">

                    <td id="td-count" class="{{ $class_N }}">
                        {{ $loop->iteration }}
                    </td>

                    <td class="{{ $class_user ?? '' }}">
                        {{ $estudiant->ci_estudiant ?? '' }}
                    </td>

                    <td id="td-users-username-{{ $estudiant->id }}" class="{{ $class_user ?? '' }}">
                        <a class="btn-link text-dark small"
                            href="{{ route('administracion.estudiants.index', ['search' => $estudiant->ci_estudiant]) }}">
                            {{ $estudiant->fullname }}<br>
                        </a>
                    </td>


                    <td class="td-nota td-nota{{ $estudiant->id ?? '' }}">
                        {!! Form::select($name, $list_nota, $value, [
                            'class' => 'obj-select-' . $estudiant->id,
                            'id' => $name,
                            'placeholder' => '',
                            'tabindex' => $loop->iteration,
                        ]) !!}
                    </td>

                    <td class="td-nota{{ $estudiant->id ?? '' }} text-center">
                        <ul class="list-group list-group-horizontal px-2">
                            @foreach ($evaluacions as $evaluacion)
                                @php $nota = $estudiant->getNotaEvaluacion($evaluacion->id); @endphp
                                {{-- @if ($nota) --}}
                                <li class="list-group-item p-1 text-center font-weight-bold"
                                    style="width: 2rem !important">
                                    {!! $nota ? $nota : '&nbsp;' !!}
                                </li>
                                {{-- @endif --}}
                            @endforeach
                        </ul>
                    </td>

                </tr>
            @endforeach

        </tbody>

    </table>

</fieldset>

<fieldset {{ empty($notasXlsCol) ? 'disabled' : null }}>
    <div class="btn-group btn-block">
        <button type="submit" class="btn-boletin btn btn-primary w-100">
            <i class="fa fa-save" aria-hidden="true"></i>
            Guardar
        </button>
    </div>
</fieldset>


{{-- partials contentivo de los scripts datatables --}}
{{-- @include('administracion.datatables.simple') --}}
