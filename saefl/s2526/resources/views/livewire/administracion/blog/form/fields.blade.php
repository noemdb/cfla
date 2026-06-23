<div>

    <form>

        <div class="form-group">
            @php
                $name = 'icon';
                $model = 'post.' . $name;
            @endphp
            <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::select($model, $list_icon, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'id' => $model,
                'placeholder' => 'Selecciones',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            @php
                $name = 'category_id';
                $model = 'post.' . $name;
            @endphp
            <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::select($model, $list_categories, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'id' => $model,
                'placeholder' => 'Selecciones',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            @php
                $name = 'title';
                $model = 'post.' . $name;
            @endphp
            <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::text($name, old($name), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment[$name],
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            @php
                $name = 'description';
                $model = 'post.' . $name;
            @endphp
            <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::text($name, old($name), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment[$name],
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            @php
                $name = 'body';
                $model = 'post.' . $name;
            @endphp
            <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::textarea($name, old($name), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment[$name],
                'rows' => '6',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group">
            @php
                $name = 'insert';
                $model = 'post.' . $name;
            @endphp
            <label for="footer" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::textarea($name, old($name), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment[$name],
                'rows' => '8',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
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
                    @php $file_url = ($post->file_url) ? asset($post->file_url) : null ; @endphp
                    @php $url_ima = ($image) ? $image->temporaryUrl() : $file_url ; @endphp
                    @if ($url_ima)
                        <center>
                            <div class="">
                                <div class="text-muted">Vista previa:</div>
                                <div class="card" style="width: 18rem;">
                                    <img src="{{ $url_ima }}" class="card-img-top" alt="...">
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

        <div class="form-group">
            @php
                $name = 'order';
                $model = 'post.' . $name;
            @endphp
            <label for="{{ $model }}"
                class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::selectRange($model, 1, 10, old($model), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'id' => $model,
                'placeholder' => 'Selecciones',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="col mb-2">
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        @php
                            $name = 'status_priority';
                            $model = 'post.' . $name;
                        @endphp
                        <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                    </div>
                </div>
                <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
</div>

<div class="col mb-2">
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text">
                @php
                    $name = 'status_feature';
                    $model = 'post.' . $name;
                @endphp
                <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
            </div>
        </div>
        <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="col mb-2">
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text">
                @php
                    $name = 'status_coverPage';
                    $model = 'post.' . $name;
                @endphp
                <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
            </div>
        </div>
        <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="col mb-2">
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text">
                @php
                    $name = 'status_pinned';
                    $model = 'post.' . $name;
                @endphp
                <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
            </div>
        </div>
        <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="col mb-2">
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text">
                @php
                    $name = 'status_banned';
                    $model = 'post.' . $name;
                @endphp
                <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
            </div>
        </div>
        <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="col mb-2">
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text">
                @php
                    $name = 'status_active';
                    $model = 'post.' . $name;
                @endphp
                <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
            </div>
        </div>
        <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="col mb-2">
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text">
                @php
                    $name = 'status_published';
                    $model = 'post.' . $name;
                @endphp
                <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
            </div>
        </div>
        <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="col mb-2">
    <div class="input-group">
        <div class="input-group-prepend">
            <div class="input-group-text">
                @php
                    $name = 'status_help';
                    $model = 'post.' . $name;
                @endphp
                <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
            </div>
        </div>
        <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
</div>

<div class="row">
    <div class="col">
        <div class="form-group">
            @php
                $name = 'created_at';
                $model = 'post.' . $name;
            @endphp
            <label for="{{ $model }}"
                class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::date($name, old($name), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment[$name],
                'id' => $name,
                'required',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="col">
        <div class="form-group">
            @php
                $name = 'updated_at';
                $model = 'post.' . $name;
            @endphp
            <label for="{{ $model }}"
                class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            {!! Form::date($name, old($name), [
                'wire:model.defer' => $model,
                'class' => 'form-control',
                'placeholder' => $list_comment[$name],
                'id' => $name,
                'required',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
</div>

<div class="btn-group btn-block" role="group" aria-label="">
    <button type="button" class="btn btn-primary w-75" wire:click="savePost()">Guardar</button>
    <button type="button" class="btn btn-dark w-25" wire:click="close()">Cerrar</button>
</div>
</form>
</div>
