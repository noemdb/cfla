<div class="p-1 border rounded shadow-sm">

    <div class="text-end p-1 m-1">
        <button type="button" class="btn-close border rounded bg-secondary" data-bs-dismiss="alert" aria-label="Close"
            wire:click="close"></button>
    </div>

    @php $pevaluacion = ($lesson) ? $lesson->pevaluacion : null; @endphp
    <div class="text-muted border-bottom">{{ $pevaluacion ? $pevaluacion->microname : null }}</div>

    <div class="px-2 small fw-light text-truncate">
        {{ $lesson->order ?? null }}.
        {{ $lesson->content ?? null }}
    </div>

    <form wire:submit.prevent="save" class="text-start  p-2 m-2">

        <div class="form-group mb-3">
            @php
                $name = 'observations';
                $model = '' . $name;
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

        <button type="submit" class="btn btn-primary btn-block w-100">Guardar</button>

    </form>

</div>
