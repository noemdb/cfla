@if ($question->option_max > $question->options->count() || $mode == 'editOption')
    <div class="p-2 m-2">

        <div class="alert alert-secondary mb-4 fw-bold">Pregunta: {!! $question->text !!}</div>
        <hr>
        <div class="d-flex justify-content-between">
            <div class="fw-bold">Registrar nueva opción:</div>
            <button type="button" class="btn btn-secondary" wire:click="close">X</button>
        </div>

        <div class="form-group">
            @php
                $name = 'text';
                $model = 'option.' . $name;
            @endphp
            <label for="{{ $name }}" class=" fw-bold m-0 small">{{ $list_comment_option[$name] ?? '' }}</label>
            {!! Form::textarea($name, old($name), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment_option[$name],
                'rows' => '2',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            @php
                $name = 'observation';
                $model = 'option.' . $name;
            @endphp
            <label for="{{ $name }}" class=" fw-bold m-0 small">{{ $list_comment_option[$name] ?? '' }}</label>
            {!! Form::textarea($name, old($name), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment_option[$name],
                'rows' => '2',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="input-group py-2">
            <span class="input-group-text" id="basic-addon1">
                @php
                    $name = 'status_option_correct';
                    $model = 'option.' . $name;
                @endphp
                <input type="checkbox" wire:model="{{ $model ?? null }}" value="1">
            </span>
            <div class="form-control"> {{ $list_comment_option[$name] ?? null }}</div>
        </div>

        <div class="btn-group w-100" role="group" aria-label="Basic example">
            <button type="button" class="btn btn-primary" wire:click="saveOption()"
                wire:loading.class="disabled">Guardar</button>
            {{-- <button type="button" class="btn btn-info" wire:click="close()">Nueva pregunta</button> --}}
        </div>

    </div>
@else
    @include('livewire.general.educational.competition.debate.partials.option_max')

    <div class="btn-group w-100" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-info" wire:click="close()">Nueva pregunta</button>
    </div>
@endif



<ul class="list-group">
    @php $options = $question->options;  @endphp
    <li class="list-group-item list-group-item-info fw-bold">Opciones registradas:</li>
    @forelse ($options as $item)
        <li
            class="list-group-item {{ $item->status_option_correct ? 'list-group-item-success font-weight-bold' : null }}">
            <div class="d-flex justify-content-between">
                <div class="text-muted fw-bold">{{ $loop->iteration }}. {!! $item->text !!}</div>
                @if (!$item->status_option_correct)
                    <div class="text-muted small">
                        <i class="fa fa-eye" aria-hidden="true" wire:click="mark({{ $item->id }})"></i>
                    </div>
                @endif
            </div>
        </li>
    @empty
        <li class="list-group-item disabled">No hay opciones</li>
    @endforelse
</ul>
