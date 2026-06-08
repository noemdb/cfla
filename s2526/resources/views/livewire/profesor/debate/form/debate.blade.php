<div class="p-2 m-2">

    <div class="row">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'grado_id';
                    $model = 'debate.' . $name;
                @endphp
                <label for="{{ $name }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment_debate[$name] ?? '' }}</label>
                {!! Form::select($model, $list_grado, old($model), [
                    'wire:model' => $model,
                    'class' => 'form-control',
                    'id' => $model,
                    'placeholder' => 'Selecciones',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>

        @if ($debate->grado_id)
            <div class="col">
                <div class="form-group">
                    @php
                        $name = 'seccion_id';
                        $model = 'debate.' . $name;
                    @endphp
                    <label for="{{ $name }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment_debate[$name] ?? '' }}</label>
                    {!! Form::select($model, $list_seccion, old($model), [
                        'wire:model.defer' => $model,
                        'class' => 'form-control',
                        'id' => $model,
                        'placeholder' => 'Selecciones',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        @endif

    </div>

    <div class="form-group">
        @php
            $name = 'name';
            $model = 'debate.' . $name;
        @endphp
        <label for="{{ $name }}"
            class=" font-weight-bold m-0 small">{{ $list_comment_debate[$name] ?? '' }}</label>
        {!! Form::text($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment_debate[$name],
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="form-group">
        @php
            $name = 'description';
            $model = 'debate.' . $name;
        @endphp
        <label for="{{ $name }}"
            class=" font-weight-bold m-0 small">{{ $list_comment_debate[$name] ?? '' }}</label>
        {!! Form::textarea($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment_debate[$name],
            'rows' => '2',
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

    <div class="row py-1">
        <div class="col">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        @php
                            $name = 'status_active';
                            $model = 'debate.' . $name;
                        @endphp
                        <input type="checkbox" wire:model="{{ $model ?? null }}" value="1">
                    </div>
                </div>
                <div class="form-control"> {{ $list_comment_debate[$name] ?? null }}</div>
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="form-group">
        @php
            $name = 'question_max';
            $model = 'debate.' . $name;
        @endphp
        <label for="{{ $name ?? null }}"
            class=" font-weight-bold m-0 small">{{ $list_comment_debate[$name] ?? '' }}</label>
        {!! Form::number($name, old($name), [
            'wire:model.defer' => $model,
            'class' => 'form-control',
            'placeholder' => $list_comment_debate[$name],
        ]) !!}
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>

</div>
