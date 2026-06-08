<div class="card">
    <div class="card-body">
        <h4 class="card-title">Ingrese un número de cédula de identidad</h4>
        <p class="card-text">
        <form>

            @php
                $name = 'representant_ci';
                $model = 'catchment.' . $name;
            @endphp
            <label for="{{ $model }}"
                class=" font-weight-bold m-0 small">{{ $list_comment_catchment[$name] ?? '' }}</label>
            <div class="input-group">

                <input type="number" id="{{ $model }}" name="{{ $model }}" class="form-control"
                    wire:model.defer="{{ $model }}">

                <div class="input-group-prepend">
                    <button type="button" class="btn btn-primary w-100" wire:click="search">Buscar</button>
                </div>

            </div>
            <small id="helpId" class="form-text text-muted">Sólo numeros</small>
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror

            @if ($status_consult)
                <hr>
                <div class="card mt-3">
                    <div class="card-body">
                        <div class="text-muted small py-2 text-end"><b>Nombre:</b> {{ $representant_name ?? null }},
                            <b>N. Censos:</b> {{ $catchment_count ?? null }}, <b>N. Entrevistas:</b>
                            {{ $interviews_count ?? null }}</div>
                        <div class="form-group pb-2">
                            @php
                                $name = 'catchment_id';
                                $model = 'catchment_interview.' . $name;
                            @endphp
                            <label for="{{ $model }}"
                                class="text-muted fw-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>

                            {!! Form::select($model, $list_catchment, old($model), [
                                'wire:model' => $model,
                                'class' => 'form-select',
                                'id' => $model,
                                'placeholder' => 'Seleccione',
                            ]) !!}
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>

                        <hr>

                        <button type="button" class="btn btn-success w-100" wire:click="create">Comenzar</button>
                    </div>
                </div>
            @endif

        </form>
        </p>
    </div>
</div>
