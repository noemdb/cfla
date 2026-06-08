@php
    $class_N = '';
    $class_estudiant = '';
    $class_ci = 'd-none d-md-table-cell';
    $class_planpago = 'd-none d-lg-table-cell text-nowrap';
    $class_deuda = 'd-none d-lg-table-cell text-nowrap';
    $class_grado = 'd-none d-lg-table-cell';
    $class_fecha = 'text-nowrap';
    $class_action = '';
@endphp

<div class="px-2">

    <div class="form-row">
        <div class="col-4">
            {!! Form::label('search', 'Estudiante', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            {!! Form::text('search', $search, [
                'class' => 'form-control',
                'wire:model.debounce.500ms' => 'search',
                'placeholder' => 'Buscar Nombre o Cédula',
            ]) !!}
        </div>
        <div class="col-4">
            {!! Form::label('grado_id', 'Grado', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            {!! Form::select('grado_id', $list_grado, $grado_id, [
                'wire:model' => 'grado_id',
                'class' => 'form-control',
                'id' => 'grado_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::label('seccion_id', 'Sección', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                'wire:model' => 'seccion_id',
                'class' => 'form-control',
                'id' => 'seccion_id',
                'placeholder' => 'Seleccione',
            ]) !!}
        </div>
        <div class="col-2">
            {!! Form::label('btn_toprint', 'Imprimir en lotes', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            <div class="btn-group btn-block" role="group" aria-label="Basic example">
                <button class="btn btn-dark disabled w-75" type="button" id="btn_toprint">
                    <i class="fa fa-print" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>

    <hr>

    @php $displaModeIndex = (!$modeIndex) ? 'd-none' : null ; @endphp

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                <th class="{{ $class_estudiant }} {{ $displaModeIndex }}">N. Evaluaciones</th>
                <th class="{{ $class_estudiant }} {{ $displaModeIndex }}">Promedio</th>
                {{-- <th class="{{ $class_estudiant }} {{ $displaModeIndex }}">Solvente</th> --}}
                <th class="{{ $class_action }} text-left">Acciones</th>
            </tr>
        </thead>

        <tbody>

            @foreach ($estudiants as $estudiant)
                @php
                    $status_active = $estudiant->status_active == 'true' ? true : false;
                    // $incidents = $estudiant->incidents;
                    // $incident_agreements = $estudiant->incident_agreements;
                    $representant = $estudiant->representant;
                    $brothers = $estudiant->BrothersFormaly;
                    $evaluacions = $estudiant->getEvaluacionsPensumLapso();
                    $exchange_ammount_unexpired_bill = $estudiant->exchange_ammount_unexpired_bill;
                    $ammount = round($exchange_ammount_unexpired_bill, 2);
                    $final_promedio = $estudiant->getPromedioFinalLapsoId(3, 2);
                @endphp

                <tr data-estudiant_id="{{ $estudiant->id ?? '' }}" data-id="{{ $estudiant->id ?? '' }}">
                    {{-- font-weight-bold --}}

                    <td id="td-count" class="{{ $class_N }}">
                        {{ $loop->iteration }}
                    </td>

                    <td class="{{ $class_estudiant ?? '' }}">
                        <div>{{ $estudiant->fullname ?? null }}</div>
                        <div class=" text-sm text-muted">{{ $estudiant->ci_estudiant }} <span class="text-muted">[Edad:
                                {{ $estudiant->age ?? null }}]</span></div>
                        <div class="{{ $estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default' }}">
                            {{ $estudiant->getInscripcion()->seccion->grado->name ?? '' }}
                            {{ $estudiant->getInscripcion()->seccion->name ?? '' }}
                        </div>
                        <div class="text-sm text-muted">{{ $estudiant->gsemail }}</div>
                        <hr class="my-0 py-0">
                        <div class="ml-2">
                            <div class="text-secondary">
                                <div class="">Representante: {{ $representant->name ?? null }} <span
                                        class="text-muted">{{ $representant->ci_representant ?? null }}</span></div>
                            </div>
                            @if ($brothers->count())
                                <div class=" text-secondary">Hermano(s):</div>
                                @foreach ($brothers as $brother)
                                    <div class="text-muted">-. {{ $brother->fullname ?? null }} <span
                                            class="text-muted">{{ $brother->ci_estudiant ?? null }}</span> <span
                                            class="text-muted">[Edad: {{ $brother->age ?? null }}]</span></div>
                                @endforeach
                            @endif
                        </div>
                    </td>

                    <td class="{{ $class_estudiant ?? '' }} {{ $displaModeIndex }}">
                        {{ $evaluacions->count() ?? null }}
                    </td>

                    <td class="{{ $class_estudiant ?? '' }} {{ $displaModeIndex }}">
                        {{ $final_promedio ?? null }}
                    </td>

                    <td class="{{ $class_action ?? '' }}">
                        <div class="btn-group btn-group-sm">

                            <a title="Mostrar resumen" class="btn btn-info bnt-sm"
                                wire:click="viewSumaries({{ $estudiant->id }})" href="#" role="button">
                                <i class="{{ $icon_menus['info'] ?? '' }} fa-1x"></i>
                            </a>

                        </div>
                    </td>
                </tr>
            @endforeach

        </tbody>

    </table>

    {{-- {{ $estudiants->links() }} --}}

    {{ $estudiants->links() }}

</div>
