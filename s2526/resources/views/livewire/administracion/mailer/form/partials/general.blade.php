<ul class="list-group list-group-flush">
    <li class="list-group-item">
        <div class="row">

            <div class="col">
                <div class="form-group">
                    <label for="name" class=" font-weight-bold m-0 small">{{ $list_comment['name'] ?? '' }}</label>
                    {!! Form::text('name', old('name'), [
                        'wire:model.defer' => 'name',
                        'class' => 'form-control',
                        'placeholder' => $list_comment['name'],
                    ]) !!}
                    @error('name')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>

            <div class="col">
                <div class="form-group">
                    <label for="code" class=" font-weight-bold m-0 small">{{ $list_comment['code'] ?? '' }}</label>
                    {!! Form::text('code', old('code'), [
                        'wire:model.defer' => 'code',
                        'class' => 'form-control',
                        'placeholder' => $list_comment['code'],
                    ]) !!}
                    @error('code')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>

        </div>

        <div class="row">
            <div class="row pb-1">
                <div class="col-12">
                    <div class="form-group">
                        <label for="autoridad_id"
                            class=" font-weight-bold m-0 small">{{ $list_comment['autoridad_id'] ?? 'autoridad_id' }}</label>
                        {!! Form::select('autoridad_id', $list_autoridads, old('autoridad_id'), [
                            'wire:model.defer' => 'autoridad_id',
                            'class' => 'form-control',
                            'id' => 'autoridad_id',
                            'placeholder' => 'Selecciones',
                        ]) !!}
                        @error('autoridad_id')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="fecha" class=" font-weight-bold m-0 small">{{ $list_comment['fecha'] ?? '' }}</label>
                    {!! Form::date('fecha', old('fecha'), [
                        'wire:model.defer' => 'fecha',
                        'class' => 'form-control',
                        'placeholder' => 'Objetivo',
                        'id' => 'fecha',
                    ]) !!}
                    @error('fecha')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col">
                <div class="form-group">
                    <label for="time" class=" font-weight-bold m-0 small">{{ $list_comment['time'] ?? '' }}</label>
                    {!! Form::time('time', old('time'), [
                        'wire:model.defer' => 'time',
                        'class' => 'form-control',
                        'placeholder' => 'Objetivo',
                        'id' => 'time',
                    ]) !!}
                    @error('time')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col">
                <div class="form-group">
                    <label for="description"
                        class=" font-weight-bold m-0 small">{{ $list_comment['description'] ?? '' }}</label>
                    {!! Form::textarea('description', old('description'), [
                        'wire:model.defer' => 'description',
                        'class' => 'form-control',
                        'placeholder' => $list_comment['description'],
                        'rows' => '2',
                    ]) !!}
                    @error('description')
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- @if (!$status_quota) --}}
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    @php
                        $name = 'cuotas';
                        $model = '' . $name;
                    @endphp
                    <label for="{{ $model }}" class=" font-weight-bold m-0 small">Cuotas</label>
                    {!! Form::selectRange($name, 1, 13, old($name), [
                        'wire:model' => $name,
                        'class' => 'form-control',
                        'placeholder' => '',
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
        {{-- @endif         --}}

        <div class="row">
            <div class="col-12">
                {{-- 
                @if (!$status_quota)
                    <div class="form-group">
                        @php $name = 'ci_list' ; $model = 'poll_main.'.$name @endphp
                        <label for="{{$model}}" class=" font-weight-bold m-0 small">{{$list_comment[$name] ?? ''}}</label>
                        {!! Form::textarea($name, old($name), ['wire:model.defer'=>$name,'class' => 'form-control','rows' => '6','placeholder'=>$list_comment[$name]]) !!}
                        @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
                    </div>
                @else
                    <div>Listado de CI.</div>
                    <div class="alert alert-secondary text-wrap" style="word-wrap: break-word;">
                        {{$ci_list}}
                    </div>
                @endif
                --}}

                <div class="form-group">
                    @php
                        $name = 'ci_list';
                        $model = '' . $name;
                    @endphp
                    <label for="{{ $model }}"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::textarea($name, old($name), [
                        'wire:model.defer' => $name,
                        'class' => 'form-control',
                        'rows' => '6',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

            </div>
        </div>

        <div class="row">
            <div class="col-12 mb-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_email';
                                $model = '' . $name;
                            @endphp
                            {{-- <input type="checkbox" wire:model.defer="{{$model ?? null}}"> --}}
                            <input type="checkbox" wire:model.defer="{{ $model ?? null }}">
                        </div>
                    </div>
                    <div class="form-control small">@include('svg.mail', ['svg_class' => 'text-danger']) Correo Electrónico (Google Gmail)
                    </div>
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="col-12 mb-2">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_whatsapp';
                                $model = '' . $name;
                            @endphp
                            {{-- <input type="checkbox" wire:model.defer="{{$model ?? null}}"> --}}
                            <input type="checkbox" wire:model="{{ $model ?? null }}">
                        </div>
                    </div>
                    <div class="form-control small">@include('svg.whatsapp', ['svg_class' => 'text-success']) Mensajería WAB (Meta - WhatsApp
                        Business)</div>
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>

                @if ($status_whatsapp)
                    <div class="form-group">
                        <label for="template"
                            class=" font-weight-bold m-0 small">{{ $list_comment['template'] ?? 'template' }}</label>
                        {!! Form::select('template', $list_template, old('template'), [
                            'wire:model.defer' => 'template',
                            'class' => 'form-control',
                            'id' => 'template',
                            'placeholder' => 'Selecciones',
                        ]) !!}
                        @error('template')
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>

                    <div class="form-group">
                        @php
                            $name = 'general';
                            $model = '' . $name;
                        @endphp
                        <label for="{{ $model }}"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::textarea($name, old($name), [
                            'wire:model.defer' => $name,
                            'class' => 'form-control',
                            'rows' => '6',
                            'placeholder' => $list_comment[$name],
                        ]) !!}
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

            </div>
        </div>
    </li>

    <li class="list-group-item">
        @include('livewire.administracion.mailer.form.partials.group')
    </li>

</ul>
