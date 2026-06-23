<div>

    <div class="border rounded shadow-lg">

        <div class="alert alert-secondary fw-bold text-muted small">{{ $lapso ? $lapso->name : null }}</div>

        @if (count($list_pevaluacions))
            <form wire:submit.prevent="saveLesson" class="text-start  p-2 m-2">

                <div class="form-group mb-3">
                    @php
                        $name = 'pevaluacion_id';
                        $model = $name;
                        $comment = $list_comment[$name];
                    @endphp
                    <label for="{{ $model }}" class="fw-bold m-0 small text-muted">{{ $comment ?? '' }}</label>
                    {!! Form::select($model, $list_pevaluacions, old($model), [
                        'wire:model' => $model,
                        'class' => 'form-select',
                        'id' => $model,
                        'placeholder' => 'Selecciones',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror

                    @if ($pevaluacion_id)
                        <div class="text-muted p-2 mx-2 mt-2 border rounded">
                            <div class="small fw-light">Lecciones registradas:</div>
                            @forelse ($items as $item)
                                <div class="px-2 small fw-light text-truncate">{{ $item->order ?? null }}.
                                    {{ $item->content ?? null }}</div>
                            @empty
                                <div class="small fw-light text-center fst-italic">No hay lecciones registradas</div>
                            @endforelse
                        </div>
                    @endif

                </div>

                @if ($pevaluacion_id)

                    @include('livewire.movile.profesor.learning.form.lesson')

                    @if ($list_evaluacions->isNotEmpty())
                        <button type="submit" class="btn btn-primary btn-block w-100">Guardar</button>
                    @endif

                @endif

            </form>
        @else
            <div class="alert alert-warning" role="alert">
                <strong>Los planes de evaluación para el {{ $lapso ? $lapso->name : null }} no han sido asignados por
                    la unidad operativa responsables.</strong>
            </div>

        @endif

    </div>

</div>
