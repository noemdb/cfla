{{-- 'poll_question_id','text','description','observations','body' --}}

<div class="container-fluid">

    <div class="row pb-1">
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'poll_question_id';
                    $model = 'poll_option.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_poll_question, old($model), [
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
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'text';
                    $model = 'poll_option.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::text($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    <div class="row pb-1">
        <div class="col-12">
            @php
                $name = 'image';
                $model = '' . $name;
            @endphp
            <label for="{{ $name ?? null }}"
                class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            <div class="custom-file">
                <input type="file" wire:model.defer="{{ $name }}" id="{{ $model }}"
                    class="custom-file-input" id="customFile">
                <label class="custom-file-label" for="customFile">{{ $list_comment[$name] ?? '' }} <span
                        class=" text-muted small font-weight-bold"> 512x512 px</span> </label>
                @php $poll_image = ($poll_option) ? $poll_option->image_url : null ; @endphp
                @php $url_ima = ($image) ? $image->temporaryUrl() : $poll_image ; @endphp
                @if ($url_ima)
                    <center>
                        <div class="">
                            <div class="text-muted">Vista previa:</div>
                            <div class="card" style="width: 18rem;">
                                <img src="{{ $url_ima }}" class="card-img-top" alt="...">
                                {{-- <img src="{{ $image->temporaryUrl() }}" class="card-img-top" alt="..."> --}}
                            </div>
                        </div>
                    </center>
                @endif
            </div>
            @error($name)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="row pb-1">
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'description';
                    $model = 'poll_option.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::textarea($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                    'rows' => '2',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'observations';
                    $model = 'poll_option.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::textarea($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                    'rows' => '4',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'body';
                    $model = 'poll_option.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::textarea($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                    'rows' => '4',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

</div>
