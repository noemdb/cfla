<div class="container-fluid">
    <div class="row">
        <div class="col-8">

            <div class="p-2 m-2">

                <div class="form-group">
                    @php
                        $name = 'pensum_id';
                        $model = 'question.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::select($model, $list_pensum, old($model), [
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
                        $name = 'category';
                        $model = 'question.' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::select($model, $list_category, old($model), [
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
                        $name = 'text';
                        $model = 'question.' . $name;
                    @endphp
                    <label for="{{ $name ?? null }}"
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

                <div class="form-group">
                    @php
                        $name = 'observation';
                        $model = 'question.' . $name;
                    @endphp
                    <label for="{{ $name ?? null }}"
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

                <div class="form-group">
                    @php
                        $name = 'time';
                        $model = 'question.' . $name;
                    @endphp
                    <label for="{{ $name ?? null }}"
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

                <div class="form-group">
                    @php
                        $name = 'weighting';
                        $model = 'question.' . $name;
                    @endphp
                    <label for="{{ $name ?? null }}"
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

                @admin
                    <div class="form-group">
                        @php
                            $name = 'option_max';
                            $model = 'question.' . $name;
                        @endphp
                        <label for="{{ $name ?? null }}"
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

                    <div class="row py-1">
                        <div class="col">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        @php
                                            $name = 'status_active';
                                            $model = 'question.' . $name;
                                        @endphp
                                        <input type="checkbox" wire:model="{{ $model ?? null }}" value="1">
                                    </div>
                                </div>
                                <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                                @error($model)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row pb-1">
                        <div class="col-12">
                            @php
                                $name = 'attachment';
                                $model = 'question.' . $name;
                            @endphp
                            <label for="{{ $name ?? null }}"
                                class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                            <div class="custom-file">
                                <input type="file" wire:model.defer="{{ $name }}" id="{{ $model }}"
                                    class="custom-file-input" id="customFile">
                                <label class="custom-file-label" for="customFile">{{ $list_comment[$name] ?? '' }} <span
                                        class=" text-muted small font-weight-bold"> 512x512 px</span> </label>
                                @php $attachment_url = ($attachment) ? $attachment->temporaryUrl() : $question->attachment_url ; @endphp
                                {{-- @php $url = ($question) ? $question->attachment : null ; @endphp --}}
                                @if ($attachment_url)
                                    <center>
                                        <div class="">
                                            <div class="text-muted">Vista previa:</div>
                                            <div class="card" style="width: 18rem;">
                                                <img src="{{ asset($attachment_url) }}" class="card-img-top"
                                                    alt="...">
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
                @endadmin

            </div>

        </div>

        <div class="col-4">

            {{-- <livewire:administracion.educational.option-component :question_id="$question_id"/> --}}

            @php $options = ($question_id) ? $question->options: collect(); @endphp
            @include('livewire.administracion.educational.partials.options')

        </div>

    </div>
</div>
