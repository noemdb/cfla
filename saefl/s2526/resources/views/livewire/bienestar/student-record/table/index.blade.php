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

    <div class="form-row">
        <div class="col-4">
            {!! Form::label('fecha', 'Estudiante', ['class' => 'm-0 font-weight-bold text-muted']) !!}
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
                <button class="btn btn-dark" type="button" id="btn_toprint">
                    <i class="fa fa-print" aria-hidden="true"></i>
                </button>
            </div>
        </div>
    </div>


    {{--     <div class="input-group">
        {!! Form::text('search', $search, ['class' => 'form-control','wire:model.debounce.500ms'=>'search','placeholder'=>'Buscar Nombre o Cédula']); !!}
        <div class="input-group-append" style="z-index: 0;">
            {!! Form::button('Buscar',['wire:click'=>'render','class'=>'btn btn-info my-2 my-sm-0','id'=>'btnSearch']); !!}
        </div>
    </div> --}}

    <hr>

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_estudiant }}">Estudiante</th>
                {{-- <th class="{{ $class_estudiant }}">Nombres</th> --}}
                {{-- <th class="{{ $class_estudiant }}">Apellidos</th> --}}
                {{-- <th class="{{ $class_grado }}">Grado/Sección</th> --}}
                <th class="{{ $class_action }}">Registrada</th>
                <th class="{{ $class_action }}">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach ($estudiants as $estudiant)
                @php
                    $status_active = $estudiant->status_active == 'true' ? true : false;
                    $student_record = $estudiant->student_record;
                    $representant = $estudiant->representant;
                    $brothers = $estudiant->BrothersFormaly;
                @endphp
                <tr data-estudiant_id="{{ $estudiant->id ?? '' }}" data-id="{{ $estudiant->id ?? '' }}"
                    class="{{ $estudiant->id == $estudiant_id ? 'table-secondary' : null }}"> {{-- font-weight-bold --}}

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
                    <td class="{{ $class_action ?? '' }}">
                        {{ $student_record ? 'SI' : 'NO' }}
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
                                    href="{{ route('bienestars.student_records.pdf.ficha', $id) }}" role="button">
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


@section('scripts')
    @parent
    <script>
        $(document).ready(function() {
            $('#btn_toprint').click(function(e) {
                e.preventDefault();
                var grado_id = $('#grado_id').val();
                var seccion_id = $('#seccion_id').val();
                var dataString = '?grado_id=' + grado_id + '&seccion_id=' + seccion_id;
                var url = "{{ route('bienestars.student_records.pdf.batch') }}" + dataString;
                window.open(url, '_blank');
            });
        });
    </script>
@endsection
