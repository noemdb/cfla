<div>

    <div class="p-1 border rounded shadow-lg">
        @php $lapso = ($pevaluacion) ? $pevaluacion->lapso : null @endphp

        <div class="alert alert-warning alert-dismissible fade show" role="alert" wire:click="close()">
            <strong>{{ $lapso ? $lapso->name : null }}</strong>
            <button type="button" class="close" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>

        <form wire:submit.prevent="saveLesson" class="text-start  p-2 m-2">

            <div class="alert alert-secondary" role="alert">
                <div class="font-weight-bold">
                    PLan de Evaluación:

                    <div class="p-2">
                        <strong>{{ $pevaluacion->asignatura_name }}</strong>

                        <div class="container-fluid">
                            <div class="row">
                                <div class="col">
                                    <div class="text-muted px-2 mx-2 mt-0 border rounded">
                                        <div class="small font-weight-light">Lecciones registradas:</div>
                                        @php $items = $pevaluacion->lessons; @endphp
                                        @forelse ($items as $item)
                                            <div class="px-2 small font-weight-light text-truncate">
                                                {{ $item->order ?? null }}. {{ $item->content ?? null }}</div>
                                        @empty
                                            <div class="small font-weight-light text-center fst-italic">No hay lecciones
                                                registradas</div>
                                        @endforelse
                                    </div>

                                    <ul class="list-group">
                                        <li class="list-group-item"><span
                                                class=" font-weight-bold">{{ $list_comment['content'] ?? '' }}</span>:
                                            <span class=" font-weight-normal"> {{ $lesson->title ?? '' }}</span></li>
                                        <li class="list-group-item"><span
                                                class=" font-weight-bold">{{ $list_comment['comments'] ?? '' }}</span>:
                                            <span class=" font-weight-normal"> {{ $lesson->comments ?? '' }}</span></li>
                                        <li class="list-group-item"><span
                                                class=" font-weight-bold">{{ $list_comment['status'] ?? '' }}</span>:
                                            <span class=" font-weight-normal">
                                                {{ $lesson->status ? 'SI' : 'NO' }}</span></li>
                                        <li class="list-group-item list-group-item-info"><span
                                                class=" font-weight-bold">{{ $list_comment['observations'] ?? '' }}</span>:
                                            <span class=" font-weight-normal"> {{ $lesson->observations ?? '' }}</span>
                                        </li>
                                    </ul>
                                </div>
                                @if (!empty($lesson->evidence))
                                    <div class="col">
                                        <div class=" font-weight-bold text-center">Evidencia:</div>
                                        <div class="d-flex justify-content-center">
                                            <img src="{{ asset($lesson->evidence_url) ?? null }}"
                                                class="img-fluid img-thumbnail shadow-sm" alt="">
                                        </div>

                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>

            </div>

            <div class="form-group mb-3">
                <hr class="my-1 py-1">
                @php
                    $name = 'observations';
                    $model = 'lesson.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class="fw-bold form-label pb-0 mb-0 text-muted">{{ $list_comment[$name] ?? '' }}</label>
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

</div>
