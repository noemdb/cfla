<div wire:key="{{ Str::random() }}">

    @if ($list_evaluacions->isNotEmpty())

        <div class="form-group mb-3">
            @php
                $name = 'evaluacion_id';
                $model = 'lesson.' . $name;
                $comment = $list_comment[$name];
            @endphp
            <label for="{{ $model }}" class="fw-bold m-0 small text-muted">{{ $comment ?? '' }}</label>
            {!! Form::select($model, $list_evaluacions, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-select',
                'id' => $model,
                'placeholder' => 'Selecciones',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-3">
            <div class="input-group mb-3">
                <div class="input-group-text p-1">
                    @php
                        $name = 'order';
                        $model = 'lesson.' . $name;
                        $comment = $list_comment[$name];
                        $start = $items->count() + 1;
                    @endphp
                    {!! Form::selectRange($model, 1, 10, old($model), [
                        'wire:model.defer' => $model,
                        'class' => 'form-select',
                        'id' => $model,
                    ]) !!}
                </div>
                <div class="form-control fw-bold text-muted small"> {{ $list_comment[$name] ?? null }}</div>
            </div>
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-3">
            <div class="input-group mb-3">
                <div class="input-group-text p-1">
                    @php
                        $name = 'status';
                        $model = 'lesson.' . $name;
                    @endphp
                    <input class="form-check-input mt-0" wire:model.defer="{{ $model ?? null }}" type="checkbox"
                        value="" aria-label="Checkbox for following text input">
                </div>
                <div class="form-control fw-light small"> {{ $list_comment[$name] ?? null }}</div>
            </div>
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-3">
            @php
                $name = 'content';
                $model = 'lesson.' . $name;
                $comment = $list_comment[$name];
            @endphp
            <label for="{{ $name }}"
                class="fw-bold form-label small pb-0 mb-0 text-muted">{{ $comment ?? null }}</label>
            {!! Form::textarea($model, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment[$name],
                'rows' => '2',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="card">
            <div class=" card-header fw-bold text-muted">Actividades de Aula</div>
            <div class="card-body">
                <div class="form-group mb-3">
                    @php
                        $name = 'teaching';
                        $model = 'lesson.' . $name;
                        $comment = $list_comment[$name];
                    @endphp
                    <label for="{{ $name }}"
                        class="fw-bold form-label small pb-0 mb-0 text-muted">{{ $comment ?? null }}</label>
                    {!! Form::textarea($model, old($model), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                        'rows' => '2',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                    <div class="small text-muted text-end">Separadas por /</div>
                </div>

                <div class="form-group mb-3">
                    @php
                        $name = 'learning';
                        $model = 'lesson.' . $name;
                        $comment = $list_comment[$name];
                    @endphp
                    <label for="{{ $name }}"
                        class="fw-bold form-label small pb-0 mb-0 text-muted">{{ $comment ?? null }}</label>
                    {!! Form::textarea($model, old($model), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                        'rows' => '2',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                    <div class="small text-muted text-end">Separados por /</div>
                </div>

            </div>
        </div>

        <div class="form-group mb-3">
            @php
                $name = 'comments';
                $model = 'lesson.' . $name;
            @endphp
            <label for="{{ $model }}"
                class="fw-bold form-label small pb-0 mb-0 text-muted">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::textarea($model, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment[$name],
                'rows' => '4',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-3">
            @php
                $name = 'planned';
                $model = 'lesson.' . $name;
                $comment = $list_comment[$name];
            @endphp
            <label for="{{ $name }}"
                class="fw-bold form-label small pb-0 mb-0 text-muted">{{ $comment ?? null }}</label>
            {!! Form::date($model, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => 'Seleccione',
                'id' => $model,
                'required',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group mb-3">
            @php
                $name = 'requireds';
                $model = 'lesson.' . $name;
            @endphp
            <label for="{{ $model }}"
                class="fw-light form-label small pb-0 mb-0 text-muted">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::textarea($model, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment[$name],
                'rows' => '4',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="row pb-1">
            <div class="col-12">
                @php $name = 'image' ; @endphp
                <div class="mb-3">
                    <label for="formFile" class="form-label">{{ $list_comment[$name] ?? '' }}</label>
                    <input class="form-control" wire:model="{{ $name }}" id="{{ $name }}"
                        type="file" id="formFile">
                </div>
                @error($name)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror

                @php $evidence_url = ( $lesson ) ? $lesson->evidence_url : null ; @endphp
                @php $url_ima = ($image) ? $image->temporaryUrl() : $evidence_url ; @endphp
                @if ($url_ima)
                    <center>
                        <div class="">
                            {{-- <div>{{$lesson ?? null}}</div> --}}
                            <div class="text-muted">Vista previa:</div>
                            <div class="card" style="width: 18rem;">
                                <img src="{{ $url_ima ?? null }}" class="card-img-top" alt="...">
                            </div>
                        </div>
                    </center>
                @endif
            </div>
        </div>
    @else
        <div class="alert alert-warning" role="alert">
            <div class="small">Los referentes teórico-práctico para el plan de evaluación seleccionado, no han sido
                cargados por el docente responsable.</div>
        </div>

    @endif


</div>
