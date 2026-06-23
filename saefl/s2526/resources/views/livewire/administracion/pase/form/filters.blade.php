<div class="border-bottom mb-2 pb-2">
    <div class="d-flex">
        @php
            $fields = [
                'selectedPestudio' => [
                    'options' => $list_pestudio,
                    'placeholder' => 'Planes de Estudio'
                ],
                'profesor_id' => [
                    'options' => $list_profesor,
                    'placeholder' => 'Profesor'
                ],
                'grado_id' => [
                    'options' => $list_grado,
                    'placeholder' => 'Grado/Año'
                ],
                'seccion_id' => [
                    'options' => $list_seccion,
                    'placeholder' => 'Sección'
                ],
                'lapso_id' => [
                    'options' => $list_lapso,
                    'placeholder' => 'Momento',
                    'id' => 'lapso_id'
                ],
                'perPage' => [
                    'options' => [
                        '10'  => '10',
                        '20'  => '20',
                        '50'  => '50',
                        '100' => '100'
                    ],
                    'placeholder' => 'Registros'
                ],
            ];
        @endphp

        @foreach ($fields as $name => $field)
            <div class="p-2 flex-grow-1">
                @php $model = $name; @endphp

                {!! Form::select(
                    $model,
                    $field['options'],
                    old($model, $model === 'perPage' ? $perPage : null),
                    array_merge(
                        [
                            'wire:model' => $model,
                            'class' => 'form-control w-100',
                            'placeholder' => $field['placeholder']
                        ],
                        isset($field['id']) ? ['id' => $field['id']] : []
                    )
                ) !!}
            </div>
        @endforeach

        <div class="px-1">
            <button type="button"
                class="btn btn-dark btn-sm w-100 p-2 mt-2"
                wire:click="clearFilters"
                title="Refrescar la página">
                <i class="fas fa-redo" aria-hidden="true"></i>
            </button>
        </div>        
    </div>

    <div class="px-1 text-right">
        <button class="btn btn-secondary btn-smp-2 mt-1"
                wire:click="openResumenModal">
            <i class="fas fa-users mr-1"></i>
            Resumen por Estudiante
        </button>
    </div>
</div>
