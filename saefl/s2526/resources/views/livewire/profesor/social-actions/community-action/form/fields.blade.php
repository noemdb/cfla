{{-- user_id,grado_id,title,description,observations,date,duration,status,type,entity_benefic,location,required,image --}}

<div class="container-fluid border-bottom">

    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'title';
                    $model = 'community_action.' . $name;
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

    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'description';
                    $model = 'community_action.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
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
    </div>

    <div class="row py-1">

        <div class="col">
            <div class="form-group">
                @php
                    $name = 'date';
                    $model = 'community_action.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::date($model, old($model), ['wire:model.defer' => $model, 'class' => 'form-control', 'id' => $model]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>

        <div class="col">
            <div class="form-group">
                @php
                    $name = 'duration';
                    $model = 'community_action.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::number($name, old($name), [
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

    <div class="row py-1">

        <div class="col">
            <div class="form-group">
                @php
                    $name = 'status';
                    $model = 'community_action.' . $name;
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

        <div class="col">
            <div class="form-group">
                @php
                    $name = 'type';
                    $model = 'community_action.' . $name;
                @endphp
                <label for="{{ $model }}"
                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::select($model, $list_type, old($model), [
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

    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'entity_benefic';
                    $model = 'community_action.' . $name;
                @endphp
                <label for="{{ $model }}" class="m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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

    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'location';
                    $model = 'community_action.' . $name;
                @endphp
                <label for="{{ $model }}" class="m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
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
    </div>

    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'required';
                    $model = 'community_action.' . $name;
                @endphp
                <label for="{{ $model }}" class="m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                {!! Form::textarea($model, old($model), [
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
    </div>

    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'observations';
                    $model = 'community_action.' . $name;
                @endphp
                <label for="{{ $model }}" class="m-0 small">{{ $list_comment[$name] ?? '' }}</label>
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
        </div>
    </div>

    <div class="row py-1">
        <div class="col">
            <div class="form-group">
                @php
                    $name = 'image';
                    $model = $name;
                @endphp
                <label for="{{ $name ?? null }}"
                    class="custom-file-label m-0">{{ $list_comment[$name] ?? '' }}</label>
                <div class="custom-file">
                    <div class="input-group mb-3">
                        <input class="custom-file-input" type="file" wire:model="{{ $name }}"
                            id="{{ $model }}" id="customFile">
                    </div>
                    {{-- @php $community_action_image = ($community_action) ? $community_action->image_url : null ; @endphp --}}
                    @php $url_ima = ($image) ? $image->temporaryUrl() : $community_action->image_url ; @endphp
                    @if ($url_ima)
                        <center>
                            <div class="">
                                <div class="text-muted">Vista previa:</div>
                                <div class="card" style="width: 18rem;">
                                    <img src="{{ $url_ima ?? null }}" class="card-img-top" alt="...">
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
    </div>


    {{-- <div class="custom-file">
            <input type="file" class="custom-file-input" id="customFile">
            <label class="custom-file-label" for="customFile">Choose file</label>
        </div> --}}
</div>
