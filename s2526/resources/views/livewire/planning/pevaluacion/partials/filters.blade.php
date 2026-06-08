<div class="border-bottom mb-2 pb-2">
    <div class="d-flex">
        @php
            $fields = [
                'pestudio_id' => ['options' => $list_pestudio, 'placeholder' => 'Planes de Estudio'],
                'profesor_id' => ['options' => $list_profesor, 'placeholder' => 'Profesor'],
                'grado_id' => ['options' => $list_grado, 'placeholder' => 'Grado/Año'],
                'seccion_id' => ['options' => $list_seccion, 'placeholder' => 'Sección'],
                'lapso_id' => ['options' => $list_lapso, 'placeholder' => 'Momento', 'id' => 'lapso_id'],
                'paginate' => [
                    'options' => ['10' => '10', '20' => '20', '50' => '50', '100' => '100'],
                    'placeholder' => 'Seleccione',
                ],
                // 'pensum_id' => ['options' => $list_pensum, 'placeholder' => 'Área de Formación', 'id' => 'pensum_id'],
            ];
        @endphp

        @foreach ($fields as $name => $field)
            <div class="p-2 flex-grow-1">
                @php $model = $name @endphp
                {!! Form::select(
                    $model,
                    $field['options'],
                    old($model, $name === 'paginate' ? $paginate : null),
                    array_merge(
                        ['wire:model' => $model, 'class' => 'form-control w-100', 'placeholder' => $field['placeholder']],
                        isset($field['id']) ? ['id' => $field['id']] : [],
                    ),
                ) !!}
            </div>
        @endforeach

        <div class="px-2">
            <button type="button" class="btn btn-dark w-100 p-2 mt-1" wire:click="resetFilters"
                title="Refrescar la página">
                <i class="fas fa-redo" aria-hidden="true"></i>
            </button>
        </div>
    </div>
</div>
