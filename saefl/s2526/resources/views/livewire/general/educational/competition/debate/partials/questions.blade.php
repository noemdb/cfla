<ul class="list-group">
    <li class="list-group-item list-group-item-info fw-bold">

        <div class="d-flex justify-content-between">
            <div>Preguntas registradas:</div>
            <div>
                <div class="d-flex">
                    <div>
                        @php
                            $name = 'filter';
                            $model = '' . $name;
                        @endphp
                        {!! Form::select($model, $list_category, old($model), [
                            'wire:model' => $model,
                            'class' => 'form-select',
                            'id' => $model,
                            'placeholder' => 'Categoría',
                        ]) !!}
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                    <div>
                        @php
                            $name = 'weighting';
                            $model = '' . $name;
                        @endphp
                        {!! Form::select($model, ['30' => '30', '50' => '50', '100' => '100'], old($model), [
                            'wire:model' => $model,
                            'class' => 'form-select',
                            'id' => $model,
                            'placeholder' => 'Ponderación',
                        ]) !!}
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>

            </div>
        </div>

    </li>

    @forelse ($questions as $item)
        @php $options = $item->options; @endphp
        <li class="list-group-item">
            <div class="text-muted fw-bold">{{ $loop->iteration }}. {!! $item->text !!}</div>
            <div class="text-muted small">
                <div class="d-flex justify-content-between align-items-start">
                    <span>Tiempo: </span> <span class="badge rounded-pill text-bg-primary"
                        title="Tiempo [Seg]">{{ $item->time }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-start">
                    <span>Ponderación:</span> <span class="badge rounded-pill text-bg-secondary"
                        title="Ponderación">{{ $item->weighting }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-start">
                    <span>Opciones:</span> <span class="badge rounded-pill text-bg-secondary"
                        title="Opciones">{{ $item->options->count() }}</span>
                </div>
                <div class="d-flex justify-content-between align-items-start">
                    <span>Categoría:</span> <span class="badge rounded-pill text-bg-light"
                        title="Opciones">{{ $item->category }}</span>
                </div>
            </div>

            <div class="d-flex justify-content-end">
                <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                    <a wire:click="editQuestion({{ $item->id }})" wire:loading.class="disabled"
                        class="btn btn-warning btn-sm" href="#" role="button">
                        <i class="bi bi-pencil-square"></i>
                    </a>
                    <button wire:click="deleteQuestion({{ $item->id }})" wire:loading.class="disabled"
                        type="button" class="btn btn-danger btn-sm {{ $options->isNotEmpty() ? 'disabled' : null }}">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>

            <div class="text-secondary small fw-bold border-bottom">Opciones:</div>
            <ul class="list-group list-group-flush small">
                @forelse ($options as $item)
                    <li
                        class="text-secondary {{ $item->status_option_correct ? 'fw-bold text-danger' : null }} small list-group-item py-0">
                        <div class="d-flex justify-content-between">
                            <div>{{ $loop->iteration }}. {!! $item->text !!}</div>

                            <div class="d-flex justify-content-end">
                                <div class="btn-group btn-group-sm" role="group" aria-label="Basic example">
                                    <a wire:click="editOption({{ $item->id }})" wire:loading.class="disabled"
                                        class="btn btn-warning btn-sm" href="#" role="button">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button wire:click="deleteOption({{ $item->id }})"
                                        wire:loading.class="disabled" type="button" class="btn btn-danger btn-sm">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>

                        </div>

                    </li>
                @empty
                    <li>No hay opciones registradas</li>
                @endforelse
            </ul>

        </li>
    @empty
        <li class="list-group-item disabled">No hay Preguntas</li>
    @endforelse
</ul>
