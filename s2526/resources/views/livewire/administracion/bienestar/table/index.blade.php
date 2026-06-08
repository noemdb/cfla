@php
    $class_N = 'd-none d-sm-table-cell';
    $class_estudiant = '';
    $class_ci = 'd-none d-md-table-cell';
    $class_planpago = 'd-none d-lg-table-cell text-nowrap';
    $class_deuda = 'd-none d-lg-table-cell text-nowrap';
    $class_grado = 'd-none d-lg-table-cell';
    $class_fecha = 'text-nowrap';
    $class_action = '';
@endphp

<div class="px-2">


    <div class="input-group">
        {!! Form::text('search', $search, [
            'class' => 'form-control',
            'wire:model.debounce.500ms' => 'search',
            'placeholder' => 'Buscar Nombre o Cédula',
        ]) !!}
        <div class="input-group-append" style="z-index: 0;">
            {!! Form::button('Buscar', [
                'wire:click' => 'render',
                'class' => 'btn btn-info my-2 my-sm-0',
                'id' => 'btnSearch',
            ]) !!}
        </div>
    </div>

    <hr>

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                {{-- <th class="{{ $class_estudiant }}">Nombres</th> --}}
                {{-- <th class="{{ $class_estudiant }}">Apellidos</th> --}}
                {{-- <th class="{{ $class_grado }}">Grado/Sección</th> --}}
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach ($estudiants as $estudiant)
                @php
                    $ammount_expire_bill = 0;
                    $status_active = $estudiant->status_active == 'true' ? true : false;
                    $student_record = $estudiant->student_record;
                @endphp
                <tr data-estudiant_id="{{ $estudiant->id ?? '' }}" data-id="{{ $estudiant->id ?? '' }}"
                    class="{{ $estudiant->id == $estudiant_id ? 'table-secondary' : null }}"> {{-- font-weight-bold --}}

                    <td id="td-count" class="{{ $class_N }}">
                        {{ $loop->iteration }}
                    </td>

                    <td class="{{ $class_estudiant ?? '' }}">
                        <div>{{ $estudiant->fullname ?? null }}</div>
                        <div class=" text-sm text-muted">{{ $estudiant->ci_estudiant }}</div>
                        <div class="{{ $estudiant->getInscripcion()->seccion->grado->class_text_color ?? 'default' }}">
                            {{ $estudiant->getInscripcion()->seccion->grado->name ?? '' }}
                            {{ $estudiant->getInscripcion()->seccion->name ?? '' }}
                        </div>
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $estudiant->id }}">

                        <div class="btn-group btn-group-sm">

                            @if ($student_record)
                                @php $id = $student_record->id @endphp
                                <a title="Editar datos de la ficha" class="btn btn-warning bnt-sm" href="#"
                                    wire:click="edit({{ $id }})" role="button">
                                    <i class="{{ $icon_menus['editar'] ?? '' }} fa-1x"></i>
                                </a>
                                <a title="Generar ficha" target="_BLANK" class="btn btn-dark bnt-sm"
                                    href="{{ route('administracion.bienestars.pdf.ficha', $id) }}" role="button">
                                    <i class="{{ $icon_menus['pdf'] ?? '' }} fa-1x"></i>
                                </a>
                            @else
                                <a title="Crear ficha del estudiante" class="btn btn-primary bnt-sm" href="#"
                                    wire:click="create({{ $estudiant->id }})" role="button">
                                    <i class="{{ $icon_menus['nuevo'] ?? '' }} fa-1x"></i>
                                </a>
                            @endif


                        </div>

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    {{ $estudiants->links() }}

</div>
