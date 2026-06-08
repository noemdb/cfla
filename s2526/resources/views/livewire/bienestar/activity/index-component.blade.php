<div>

    <h4 class="alert alert-secondary mb-0 pb-0 border-bottom">
        <div class="d-flex justify-content-between">
            <div>
                <i class="{{ $icon_menus['activities'] ?? '' }} text-info pr-1" aria-hidden="true"></i>
                <span class="font-weight-bold">Módulo Plan de Actividades</span>
                <small class="text-muted">Complementaria Integral</small>
            </div>
            <div><span class="text-muted font-weight-bold" style="font-size: 1rem;opacity: 0.5;">Diseñado por: Prof.
                    Carmin Cortez</span></div>
        </div>
    </h4>


    <div class="border-bottom my-2 py-2">
        <div class="d-flex flex-wrap">
            <div class="px-2">
                @php
                    $name = 'grado_id';
                    $model = '' . $name;
                @endphp
                {!! Form::select($model, $list_grado, $grado_id, [
                    'wire:model' => $model,
                    'class' => 'form-control px-1 mx-1',
                    'placeholder' => 'Grado/Año',
                ]) !!}
            </div>

            <div class="px-2">
                @php
                    $name = 'seccion_id';
                    $model = '' . $name;
                @endphp
                {!! Form::select($model, $list_seccion, $seccion_id, [
                    'wire:model' => $model,
                    'class' => 'form-control px-1 mx-1',
                    'placeholder' => 'Sección',
                ]) !!}
            </div>

            <div class="px-2">
                @php
                    $name = 'lapso_id';
                    $model = '' . $name;
                @endphp
                {!! Form::select($model, $list_lapso, $lapso_id, [
                    'wire:model' => $model,
                    'class' => 'form-control px-1 mx-1',
                    'id' => 'lapso_id',
                    'placeholder' => 'Momento',
                ]) !!}
            </div>

            <div class="px-2">
                <button wire:click="resetFilters" type="button" class="btn btn-outline-secondary">
                    <i class="fas fa-redo"></i> Reset
                </button>
            </div>

        </div>
    </div>

    @if ($pevaluacions && $pevaluacions->count())
        @include('livewire.bienestar.activity.table.index', ['pevaluacions' => $pevaluacions])
    @else
        <div class="alert alert-light">Seleccione un <b>Grado</b>, <b>Sección</b> o <b>Momento</b> para mostrar los
            registros.</div>
    @endif

</div>
