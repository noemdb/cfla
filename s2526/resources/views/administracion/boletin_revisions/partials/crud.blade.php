@php
    $class_N = 'd-none d-sm-table-cell';
    $class_profesor = '';
    $class_asignatura = '';
    $class_tipo = '';
    $class_grado = '';
    $class_lapso = '';
    $class_action = 'nosort';
    ///////////////////////////////////////////
    $modeEdit = isset($modeEdit) ? $modeEdit : (Request::is('*edit*') ? true : false);
    $isLivewire = isset($isLivewire) ? $isLivewire : false;
@endphp

<table width="100%" class="table table-striped table-hover table-sm small p-1">
    <thead>
        <tr>
            <th class="{{ $class_N }}">N</th>
            <th class="{{ $class_profesor }}">Profesor</th>
            <th class="{{ $class_profesor }}">Asignatura</th>
            <th class="{{ $class_tipo }}">Tipo</th>
            <th class="{{ $class_profesor }}">Nota F.</th>
            <th class="{{ $class_profesor }}">Número</th>
            <th class="{{ $class_profesor }}">Nota Revisión</th>
            <th class="{{ $class_action }} text-center">&nbsp;</th>

        </tr>
    </thead>

    <tbody id="tdatos">

        @foreach ($boletin_revisions as $item)
            @php
                $estudiant = $item->estudiant;
                $profesor = $item->profesor;
                $pestudio = $estudiant->pestudio;
                $escala = $pestudio->escala;
                $aprobacion = $escala->aprobacion ?: '';
                $pensum = $item->pensum;
                $asignatura = $pensum->asignatura;
                $nota = $estudiant->getNotaFinal($pensum->id);
                $nota_pf = is_numeric($nota) ? str_pad($nota, 2, '0', STR_PAD_LEFT) : $nota;
                $selected_id = isset($selected_id)
                    ? $selected_id
                    : ($modeEdit && isset($boletin_revision)
                        ? $boletin_revision->id
                        : null);
            @endphp
            <tr data-id="{{ $estudiant->id }}" class="{{ $selected_id == $item->id ? 'table-secondary' : null }}">
                <td id="td-count" class="{{ $class_N }}">
                    {{ $loop->iteration }}
                </td>
                <td class="{{ $class_profesor ?? '' }}">
                    {{ $profesor->fullname ?? '' }}
                </td>
                <td class="{{ $class_profesor ?? '' }}">
                    {{ $asignatura->fullname ?? '' }}
                </td>
                <td class="{{ $class_tipo ?? '' }}">
                    {{ $item->type ?? '' }}
                </td>
                <td class="{{ $class_profesor ?? '' }}">
                    {{ $nota_pf ?? '' }}
                </td>
                <td class="{{ $class_profesor ?? '' }}">
                    {{ $item->numero ?? '' }}
                </td>
                <td class="{{ $class_profesor ?? '' }}">
                    {{ $item->nota ?? '' }}
                </td>
                <td class="{{ $class_action ?? '' }}  text-right">
                    <div class="btn-group btn-group-sm">
                        @if ($isLivewire)
                            <button type="button" class="btn btn-warning btn-sm"
                                wire:click="edit({{ $item->id }})">
                                <i class="fas fa-edit"></i>
                            </button>
                        @else
                            @php $route = ($estudiant) ? route('administracion.boletin_revisions.edit',$item->id) : "#"; @endphp
                            @include('elements.buttons.crud.default.edit', ['route' => $route])
                        @endif
                    </div>
                </td>

            </tr>
        @endforeach

    </tbody>

</table>
