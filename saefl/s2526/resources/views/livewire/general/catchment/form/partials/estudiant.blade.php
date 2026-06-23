<section>
    <h2>Información General del estudiante</h2>


    <div class="form-group pb-2">
        @php
            $name = 'firstname';
            $model = 'catchment.' . $name;
        @endphp
        <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <input type="text" id="{{ $model }}" name="{{ $model }}" class="form-control"
            wire:model.defer="{{ $model }}">
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>


    <div class="form-group pb-2">
        @php
            $name = 'lastname';
            $model = 'catchment.' . $name;
        @endphp
        <label for="{{ $model }}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <input type="text" id="{{ $model }}" name="{{ $model }}" class="form-control"
            wire:model.defer="{{ $model }}">
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group pb-2">
        @php
            $name = 'grade';
            $model = 'catchment.' . $name;
        @endphp
        <label for="{{ $model }}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <select class="form-control" id="{{ $model }}" name="{{ $model }}"
            wire:model="{{ $model }}">
            <option selected="selected" value="">Seleccione</option>
            <optgroup label="20000-EDUCACION INICIAL">
                <option value="22">1ER NIVEL</option>
                <option value="23">2DO NIVEL</option>
                <option value="24">3ER NIVEL</option>
            </optgroup>
            <optgroup label="21000-EDUCACION PRIMARIA">
                <option value="1">1ER GRADO</option>
                <option value="2">2DO GRADO</option>
                <option value="3">3ER GRADO</option>
                <option value="4">4TO GRADO</option>
                <option value="5">5TO GRADO</option>
                <option value="6">6TO GRADO</option>
            </optgroup>
            <optgroup label="31059B-EDUCACION MEDIA GENERAL CIENCIA Y TECNOLOGIA">
                <option value="12">PRIMER AÑO</option>
                <option value="13">SEGUNDO AÑO</option>
                <option value="14">TERCER AÑO</option>
                <option value="15">CUARTO AÑO</option>
                {{-- <option value="16">QUINTO AÑO</option> --}}
            </optgroup>
        </select>
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
        @error('catchment.group_id')
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group pb-2">
        @php
            $name = 'date_birth';
            $model = 'catchment.' . $name;
        @endphp
        <label for="{{ $model }}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <input type="date" id="{{ $model }}" name="{{ $model }}" class="form-control"
            wire:model.defer="{{ $model }}">
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group pb-2">
        @php
            $name = 'gender';
            $model = 'catchment.' . $name;
        @endphp
        <label for="{{ $model }}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <select class="form-control" id="{{ $model }}" name="{{ $model }}"
            wire:model.defer="{{ $model }}">
            <option>Seleccione</option>
            <option value="masculino">Masculino</option>
            <option value="femenino">Femenino</option>
        </select>
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group pb-2">
        @php
            $name = 'direction';
            $model = 'catchment.' . $name;
        @endphp
        <label for="{{ $model }}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <textarea class="form-control" id="{{ $model }}" name="{{ $model }}"
            wire:model.defer="{{ $model }}"></textarea>
        @error($model)
            <span class="text-danger small mb-2">{{ $message }}</span>
        @enderror
    </div>

    <hr>

    <div class="input-group pb-2">
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    @php
                        $name = 'status_foreign';
                        $model = 'catchment.' . $name;
                    @endphp
                    <input type="checkbox" wire:model="{{ $model ?? null }}">
                </div>
            </div>
            <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
        </div>
    </div>
    @error($model)
        <span class="text-danger small">{{ $message }}</span>
    @enderror

    @if ($catchment->status_foreign)
        <div class="form-group pb-2">
            @php
                $name = 'country_foreign';
                $model = 'catchment.' . $name;
            @endphp
            <label for="{{ $model }}"
                class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            <input type="text" id="{{ $model }}" name="{{ $model }}" class="form-control"
                wire:model.defer="{{ $model }}">
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    @endif

    <div class="input-group pb-2">
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    @php
                        $name = 'status_siblings_college';
                        $model = 'catchment.' . $name;
                    @endphp
                    <input type="checkbox" wire:model="{{ $model ?? null }}">
                </div>
            </div>
            <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
        </div>
    </div>
    @error($model)
        <span class="text-danger small">{{ $message }}</span>
    @enderror

    @if ($catchment->status_siblings_college)
        <div class="form-group pb-2">
            @php
                $name = 'brothers';
                $model = 'catchment.' . $name;
            @endphp
            <label for="{{ $model }}"
                class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            <input type="number" id="{{ $model }}" name="{{ $model }}" class="form-control"
                wire:model.defer="{{ $model }}">
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    @endif

    <div class="form-group pb-2">
        @php
            $name = 'origin';
            $model = 'catchment.' . $name;
        @endphp
        <label for="{{ $model }}"
            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        @if ($catchment->status_foreign || $status_institucion_not_found)
            <input type="text" id="{{ $model }}" name="{{ $model }}" class="form-control"
                wire:model.defer="{{ $model }}">
        @else
            {!! Form::select($model, $list_oinstitucions, old($model), [
                'wire:model' => $model,
                'class' => 'form-select',
                'id' => $model,
                'placeholder' => 'Seleccione',
            ]) !!}
        @endif
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    @if (!$catchment->origin)
        <div class="input-group pb-2">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        @php
                            $name = 'status_institucion_not_found';
                            $model = '' . $name;
                        @endphp
                        <input type="checkbox" wire:model="{{ $model ?? null }}">
                    </div>
                </div>
                <div class="form-control text-muted"> <small>{{ $list_comment[$name] ?? null }}</small></div>
            </div>
        </div>
    @endif



</section>
