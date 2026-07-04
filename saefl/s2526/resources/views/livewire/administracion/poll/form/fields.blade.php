{{-- 'poll_group_id','name','description','observations','finicial'.'time','ffinal' --}}



<div class="container-fluid">

    <div class="row pb-1">
        <div class="col-6">
            <div class="form-group">
                @php
                    $name = 'name';
                    $model = 'poll_main.' . $name;
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
        <div class="col-6">
            <div class="form-group">
                @php
                    $name = 'poll_group_id';
                    $model = 'poll_main.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $poll_group_list, old($model), [
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
    </div>

    <div class="row pb-1">
        <div class="col-12">
            @php
                $name = 'image';
                $model = 'poll_main.' . $name;
            @endphp
            <label for="{{ $name ?? null }}"
                class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
            <div class="custom-file">
                <input type="file" wire:model.defer="{{ $name }}" id="{{ $model }}"
                    class="custom-file-input" id="customFile">
                <label class="custom-file-label" for="customFile">{{ $list_comment[$name] ?? '' }} <span
                        class=" text-muted small font-weight-bold"> 512x512 px</span> </label>
                @php $poll_image = ($poll_main) ? $poll_main->image_url : null ; @endphp
                @php $url_ima = ($image) ? $image->temporaryUrl() : $poll_image ; @endphp
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

    <div class="row pb-1">
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'description';
                    $model = 'poll_main.' . $name;
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
                    $model = 'poll_main.' . $name;
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
    <div class="row pb-1">
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'autoridad_id';
                    $model = 'poll_main.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_autoridads, old($model), [
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
    </div>

    <div class="row pb-1">
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'status_estudiant';
                    $model = 'poll_main.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_status, old($model), [
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
    </div>

    <div class="row pb-1">
        <div class="col-6">
            <div class="form-group">
                @php
                    $name = 'date_start';
                    $model = 'poll_main.' . $name;
                @endphp
                <label for="{{ $model }}" class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}
                    <span>{{ $poll_main->date_start ?? null }}</span></label>
                {!! Form::date($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                @php
                    $name = 'time_start';
                    $model = 'poll_main.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::time($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                @php
                    $name = 'date_end';
                    $model = 'poll_main.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::date($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
        <div class="col-6">
            <div class="form-group">
                @php
                    $name = 'time_end';
                    $model = 'poll_main.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::time($name, old($name), [
                    'wire:model.defer' => $model,
                    'class' => 'form-control',
                    'placeholder' => $list_comment[$name],
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'ci_list';
                    $model = 'poll_main.' . $name;
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
            <div class="form-group">
                @php
                    $name = 'status_exclude_last';
                    $model = 'poll_main.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_status, old($model), [
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
    </div>

    <div class="row pb-1">
        <div class="col-12">
            <div class="form-group">
                @php
                    $name = 'status_representant';
                    $model = 'poll_main.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_status, old($model), [
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
    </div>

    @admin
        <div class="row pb-1">
            <div class="col-12">
                <div class="form-group">
                    @php
                        $name = 'status_test';
                        $model = 'poll_main.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::select($model, $list_status, old($model), [
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
        </div>
    @endadmin

</div>
