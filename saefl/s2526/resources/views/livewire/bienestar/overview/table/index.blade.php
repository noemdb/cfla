@php
    $class_N = 'd-none d-sm-table-cell';
    $class_profesor = '';
    $class_ci = 'd-none d-md-table-cell';
    $class_planpago = 'd-none d-lg-table-cell text-nowrap';
    $class_deuda = 'd-none d-lg-table-cell text-nowrap';
    $class_grado = 'd-none d-lg-table-cell';
    $class_fecha = 'text-nowrap';
    $class_action = '';
@endphp

<div class="px-2">

    <div class="form-row">
        <div class="col-10">
            {!! Form::label('fecha', 'profesors', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            {!! Form::text('search', $search, [
                'class' => 'form-control',
                'wire:model.debounce.500ms' => 'search',
                'placeholder' => 'Buscar Nombre o Cédula',
            ]) !!}
        </div>
        {{-- <div class="col-2">
            @php $name = 'finicial' ; $model= ''.$name @endphp
            {!! Form::label('finicial', 'F.Inicial', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            {!! Form::date($model, old($model), ['wire:model'=>$model,'class' => 'form-control','id'=>$model]); !!}
        </div>
        <div class="col-2">
            @php $name = 'ffinal' ; $model= ''.$name @endphp
            {!! Form::label('ffinal', 'F.Final', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            {!! Form::date($model, old($model), ['wire:model'=>$model,'class' => 'form-control','id'=>$model]); !!}
        </div> --}}
        <div class="col-2">
            {!! Form::label('btn_toprint', '___', ['class' => 'm-0 font-weight-bold text-muted']) !!}
            <div class="btn-group btn-block" role="group" aria-label="Basic example">

                <a title="Refrescar" class="btn btn-info btn-sm mr-1" href="#" wire:click="goModeIndex"
                    role="button">
                    <i class="{{ $icon_menus['cronograma'] ?? '' }} fa-1x"></i>
                    Refrescar
                </a>
            </div>
        </div>
    </div>

    <hr>

    <table width="100%" class="table table-striped table-hover table-sm small p-1" id="table-data-default">
        <thead>
            <tr>
                <th class="{{ $class_N }}">N</th>
                <th class="{{ $class_profesor }}">Profesor</th>
                <th class="{{ $class_profesor }}">N. incidencias registradas</th>
                <th class="{{ $class_profesor }}">N.Convocatorias</th>
                <th class="{{ $class_action }} text-center">Acción</th>
            </tr>
        </thead>

        <tbody id="tdatos">

            @foreach ($profesors as $profesor)
                @php
                    $status_active = $profesor->status_active == 'true' ? true : false;
                    $incidents = $profesor->incidents;
                    $pevaluacions = $profesor->pevaluacions;
                @endphp
                <tr data-profesor_id="{{ $profesor->id ?? '' }}" data-id="{{ $profesor->id ?? '' }}"
                    class="{{ $profesor->id == $profesor_id ? 'table-secondary' : null }}"> {{-- font-weight-bold --}}

                    <td id="td-count" class="{{ $class_N }}">
                        {{ $loop->iteration }}
                    </td>

                    <td class="{{ $class_profesor ?? '' }}">
                        <div>{{ $profesor->fullname ?? null }}</div>
                        <div class=" text-sm text-muted">
                            {{ $profesor->ci_profesor }}
                            <span class="text-muted">[Planes de Evaluación:
                                {{ $pevaluacions->isNotEmpty() ? $pevaluacions->count() : 0 }}]</span>
                        </div>
                    </td>

                    <td class="{{ $class_profesor ?? '' }}">
                        {{ $incidents->count() ?? null }}
                    </td>

                    <td class="{{ $class_profesor ?? '' }}">
                        @php $incidents_announcement = $incidents->where('status_announcement',true) @endphp
                        {{ $incidents_announcement->count() }}
                    </td>

                    <td class="{{ $class_action ?? '' }}" id="btn-action-{{ $profesor->id }}">

                        <div class="d-flex justify-content-start">

                            <a title="Reporte semanal" wire:click="weekely({{ $profesor->id }})"
                                class="btn btn-light btn-sm mr-1" href="#" role="button">
                                <i class="{{ $icon_menus['cronograma'] ?? '' }} fa-1x"></i>
                            </a>

                        </div>

                    </td>
                </tr>
            @endforeach

        </tbody>
    </table>

    {{ $profesors->links() }}

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
                var url = "{{ route('bienestars.incidents.pdf.batch') }}" + dataString;
                // window.open(url,'_blank');
            });
        });
    </script>
@endsection
