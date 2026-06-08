<div class="container-fluid">
    <div class="row py-2">
        <div class="col-sm-12">
            <div class="form-group pb-2">
                @php
                    $name = 'text';
                    $model = 'interview_answer.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::textarea($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control alert alert-secondary p-2 fw-bold',
                    'rows' => '4',
                    'placeholder' => $list_comment[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

            <div class="alert alert-secondary p-1 m-1">
                @php
                    $count = $unansweredQuestions->count();
                    $total = $questions->count();
                @endphp
                @if ($count == $total)
                    @include('livewire.general.interview.assistant.photo')
                @endif
            </div>

            <div class="d-flex justify-content-center mt-4">
                <a class="btn btn-primary" type="button" href="#" wire:click="save" id="redirect">Responder y
                    continuar</a>
                <div wire:loading class="text-muted small fw-bold">
                    Procesando...
                </div>
            </div>

        </div>
    </div>


</div>
