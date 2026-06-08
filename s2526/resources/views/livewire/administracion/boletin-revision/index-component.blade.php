<div>

    {{-- CARD CONTENEDORA --}}
    <div class="card card-primary mt-2 position-relative">

        {{-- OVERLAY LOADING --}}
        <div wire:loading.flex class="position-absolute w-100 h-100 justify-content-center align-items-center"
            style="top:0; left:0; background:rgba(255,255,255,0.7); z-index:10;">
            <div class="text-center">
                <div class="spinner-border text-primary" role="status">
                    <span class="sr-only">Cargando...</span>
                </div>
                <div class="mt-2">
                    <small class="text-muted">Actualizando información...</small>
                </div>
            </div>
        </div>

        <div class="card-header alert-info">
            <div class="btn-group float-right">
                @if ($mode === 'index')
                    <button type="button" class="btn btn-light" wire:click="setMode('crud')"
                        title="Listado de Revisiones">
                        <i class="fas fa-list"></i> Listado de Revisiones
                    </button>
                @else
                    <button type="button" class="btn btn-light" wire:click="setMode('index')"
                        title="Listado de Estudiantes">
                        <i class="fas fa-users"></i> Listado de Estudiantes
                    </button>
                @endif

                <a href="{{ url()->previous() }}" class="btn btn-dark" title="Ir atrás">
                    <i class="fas fa-chevron-left"></i> Ir atrás
                </a>

                <a href="{{ route('administracion.boletin_revisions.index') }}" class="btn btn-dark"
                    title="Refrescar la página">
                    <i class="fas fa-redo"></i> Refrescar
                </a>
            </div>
            <h4>
                <u title="Listado especial con botones de acción">Listado</u> de estudiantes para
                <span class="font-weight-bold"> Revisión de Notas</span>
            </h4>
        </div>

        <div class="card-body" wire:loading.class="opacity-50">

            {{-- Errores y mensajes --}}
            @include('administracion.elements.forms.errors')
            @include('administracion.elements.messeges.oper_ok')

            {{-- FILTROS --}}
            <form class="form-row mb-3" wire:submit.prevent>
                <div class="form-group col-md-8">
                    <label for="grado_id">Grado</label>
                    {!! Form::select('grado_id', $list_grado, $grado_id, [
                        'wire:model' => 'grado_id',
                        'wire:loading.attr' => 'disabled',
                        'class' => 'form-control',
                        'id' => 'grado_id',
                        'placeholder' => 'Seleccione',
                        'required',
                    ]) !!}
                </div>

                <div class="form-group col-md-4">
                    <label for="seccion_id">Sección</label>
                    {!! Form::select('seccion_id', $list_seccion, $seccion_id, [
                        'wire:model' => 'seccion_id',
                        'wire:loading.attr' => 'disabled',
                        'class' => 'form-control',
                        'id' => 'seccion_id',
                        'placeholder' => 'Seleccione',
                    ]) !!}
                </div>

                {{-- <div class="form-group col-md-2 text-right">
                    <label for="accion">Acción</label>
                    <div class="btn-group btn-group-sm d-block">
                        <button type="button" class="btn btn-outline-dark {{ $mode === 'index' ? 'active' : '' }}"
                            wire:click="setMode('index')" wire:loading.attr="disabled">
                            Estudiantes
                        </button>
                        <button type="button" class="btn btn-outline-dark {{ $mode === 'crud' ? 'active' : '' }}"
                            wire:click="setMode('crud')" wire:loading.attr="disabled">
                            Revisiones
                        </button>


                    </div>
                </div> --}}
            </form>

            {{-- ===========================
                MODO INDEX: Estudiantes
            =========================== --}}
            @if ($mode === 'index')

                @php
                    $class_N = 'd-none d-sm-table-cell';
                    $class_profesor = '';
                    $class_asignatura = '';
                    $class_grado = '';
                    $class_lapso = '';
                    $class_action = 'nosort';
                @endphp

                <table width="100%" class="table table-striped table-hover table-sm small p-1"
                    id="table-data-default">
                    <thead>
                        <tr>
                            <th class="{{ $class_N }}">N</th>
                            <th class="{{ $class_profesor }}">Estudiante</th>

                            @foreach ($pensums as $pensum)
                                @php $asignatura = $pensum->asignatura; @endphp
                                <th class="{{ $class_asignatura ?? '' }} text-center">
                                    {{ $asignatura->code_sm ?? '' }}
                                </th>
                            @endforeach

                            <th class="{{ $class_asignatura }} text-center">Aplazadas</th>
                            <th class="{{ $class_asignatura }} text-center">Revisiones</th>
                            <th class="{{ $class_action }} text-center">&nbsp;</th>
                        </tr>
                    </thead>

                    <tbody id="tdatos">
                        @foreach ($estudiants as $estudiant)
                            @php
                                $pestudio = $estudiant->pestudio;
                                $escala = $pestudio ? $pestudio->escala : null;
                                $aprobacion = $escala && $escala->aprobacion ? $escala->aprobacion : '';
                                $count_revision = !empty($estudiant->boletin_revisions)
                                    ? $estudiant->boletin_revisions->count()
                                    : null;
                            @endphp
                            <tr data-id="{{ $estudiant->id }}">
                                <td id="td-count" class="{{ $class_N }}">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="{{ $class_profesor ?? '' }}">
                                    {{ $estudiant->fullname ?? '' }}<br>
                                    <small class="text-muted">{{ $estudiant->ci_estudiant ?? '' }}</small>
                                </td>

                                @php
                                    $count_aplazadas = 0;
                                    $studentGrades = $grades[$estudiant->id] ?? [];
                                    foreach ($studentGrades as $g) {
                                        if ($g['is_aplazada']) {
                                            $count_aplazadas++;
                                        }
                                    }
                                @endphp

                                @foreach ($pensums as $pensum)
                                    @php
                                        $asignatura = $pensum->asignatura;
                                        $gradeData = $studentGrades[$pensum->id] ?? null;
                                        $display = $gradeData['display'] ?? '';
                                        $is_aplazada = $gradeData['is_aplazada'] ?? false;
                                    @endphp

                                    <td
                                        class="{{ $class_asignatura ?? '' }} {{ $is_aplazada ? 'alert-danger' : '' }} text-center">
                                        {!! $display !!}
                                    </td>
                                @endforeach

                                <td class="{{ $class_asignatura ?? '' }} text-center">
                                    {{ $count_aplazadas ?? '' }}
                                </td>
                                <td class="{{ $class_asignatura ?? '' }} text-center">
                                    {{ $count_revision ?? '' }}
                                </td>
                                <td class="{{ $class_action ?? '' }} text-right">
                                    <div class="btn-group btn-group-sm">
                                        @php
                                            $routeCert = $estudiant
                                                ? route(
                                                    'administracion.historico_notas.certificacion.pdf',
                                                    $estudiant->id,
                                                )
                                                : '#';
                                            $disabledCert = $count_revision > 0 ? null : 'disabled';
                                        @endphp
                                        @include('elements.buttons.crud.default.info', [
                                            'route' => $routeCert,
                                            'disabled' => $disabledCert,
                                        ])

                                        {{-- Botón Livewire para abrir el formulario de creación,
                                            solo si tiene materias aplazadas --}}
                                        <button type="button" class="btn btn-primary btn-sm"
                                            wire:click="openCreate({{ $estudiant->id }})"
                                            @if (($count_aplazadas ?? 0) == 0) disabled @endif>
                                            <i class="far fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- ===========================
                MODO CRUD: Revisiones
                =========================== --}}
            @elseif($mode === 'crud')
                @php
                    $class_N = 'd-none d-sm-table-cell';
                    $class_profesor = '';
                    $class_asignatura = '';
                    $class_tipo = '';
                    $class_grado = '';
                    $class_lapso = '';
                    $class_action = 'nosort';
                @endphp

                <table width="100%" class="table table-striped table-hover table-sm small p-1"
                    id="table-data-default">
                    <thead>
                        <tr>
                            <th class="{{ $class_N }}">N</th>
                            <th class="{{ $class_profesor }}">Estudiante</th>
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
                        @foreach ($boletin_revisions as $boletin_revision)
                            @php
                                $estudiant = $boletin_revision->estudiant;
                                $profesor = $boletin_revision->profesor;
                                $pestudio = $estudiant->pestudio ?? null;
                                $escala = $pestudio ? $pestudio->escala : null;
                                $aprobacion = $escala && $escala->aprobacion ? $escala->aprobacion : '';
                                $pensum = $boletin_revision->pensum;
                                $asignatura = $pensum->asignatura ?? null;
                                $nota = $estudiant ? $estudiant->getNotaFinal($pensum->id) : null;
                                $nota_pf = is_numeric($nota) ? str_pad($nota, 2, '0', STR_PAD_LEFT) : $nota;
                            @endphp
                            <tr data-id="{{ $estudiant->id ?? '' }}">
                                <td id="td-count" class="{{ $class_N }}">
                                    {{ $loop->iteration }}
                                </td>
                                <td class="{{ $class_profesor ?? '' }}">
                                    {{ $estudiant->fullname ?? '' }}<br>
                                    <small class="text-muted">{{ $estudiant->ci_estudiant ?? '' }}</small>
                                </td>
                                <td class="{{ $class_profesor ?? '' }}">
                                    {{ $profesor->fullname ?? '' }}
                                </td>
                                <td class="{{ $class_profesor ?? '' }}">
                                    {{ $asignatura->fullname ?? '' }}
                                </td>
                                <td class="{{ $class_tipo ?? '' }}">
                                    {{ $boletin_revision->type ?? '' }}
                                </td>
                                <td class="{{ $class_profesor ?? '' }}">
                                    {{ $nota_pf ?? '' }}
                                </td>
                                <td class="{{ $class_profesor ?? '' }}">
                                    {{ $boletin_revision->numero ?? '' }}
                                </td>
                                <td class="{{ $class_profesor ?? '' }}">
                                    {{ $boletin_revision->nota ?? '' }}
                                </td>
                                <td class="{{ $class_action ?? '' }} text-right">
                                    <div class="btn-group btn-group-sm">
                                        <button type="button" class="btn btn-primary btn-sm"
                                            wire:click="openCreate({{ $estudiant->id }})">
                                            <i class="far fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                @include('administracion.datatables.default')

                {{-- ===========================
                 MODO CREATE: Formulario
               =========================== --}}
            @elseif($mode === 'create' && $createEstudiant)
                <form wire:submit.prevent="save" id="form-boletin-revision-create" class="form-signin">

                    <div class="card bd-callout bd-callout-primary">
                        <div class="card-header alert-secondary">
                            <div class="btn-group float-right pt-2">
                                <button type="button" class="btn btn-secondary btn-sm" wire:click="cancelCreate"
                                    wire:loading.attr="disabled">
                                    <i class="fas fa-times text-black font-weight-bold"></i>
                                </button>
                            </div>
                            <h5 class="card-title mb-0">
                                Registrar Revisión de Notas
                            </h5>
                        </div>

                        <div class="card-body">
                            <div class="row">
                                <div class="col-2">
                                    @php $estudiant = $createEstudiant; @endphp
                                    @include('administracion.boletin_revisions.cards.estudiant')
                                </div>

                                <div class="col-8 alert-secondary border rounded p-1">
                                    @php
                                        $estudiant = $createEstudiant;
                                        $pensums = $createPensums;
                                        $escala = $createEscala;
                                        $list_pensum = $createListPensum;
                                        $list_profesor = $createListProfesor;
                                        $list_nota = $createListNota;
                                        $list_comment = $createListComment;
                                    @endphp

                                    {{-- Aquí dentro los campos deben usar wire:model hacia
                                    $pensum_id, $profesor_id, $numero, $nota, etc.
                                    --}}
                                    @include('administracion.boletin_revisions.form.fields')

                                    <button type="submit" class="btn-inscripcion-create btn btn-primary btn-block"
                                        id="btn-create-inscripcion">
                                        <i class="far fa-save"></i>
                                        Registrar
                                    </button>

                                    <button type="button" class="btn btn-secondary btn-block mt-2"
                                        wire:click="cancelCreate">
                                        Cancelar
                                    </button>
                                </div>

                                <div class="col-2 text-muted">
                                    @php
                                        $estudiant = $createEstudiant;
                                        $pensums = $createPensums;
                                        $escala = $createEscala;
                                    @endphp
                                    @include('administracion.boletin_revisions.partials.resumen.pensums')
                                </div>
                            </div>

                            <hr>

                            <div class="card">
                                <div class="card-body">
                                    <h5 class="card-title text-primary">
                                        Listado de <span class="font-weight-bold">Revisiones</span> registradas
                                    </h5>
                                    <p class="card-text">
                                        @php
                                            $boletin_revisions = $createBoletinRevisions;
                                            $estudiant = $createEstudiant;
                                            $escala = $createEscala;
                                        @endphp
                                        @include('administracion.boletin_revisions.partials.crud', [
                                            'isLivewire' => true,
                                            'modeEdit' => $selected_revision_id ? true : false,
                                            'selected_id' => $selected_revision_id,
                                        ])
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            @endif

        </div>
    </div>
</div>
