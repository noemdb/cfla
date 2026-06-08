<div class="container-fluid px-1">

    <div class="row py-2">
        <div class="col-12">
            <div class="form-group text-start">@php
                $name = 'pevaluacion_id';
                $model = 'activity.' . $name;
                $comment = $list_comment[$name];
            @endphp
                <label for="{{ $model }}" class="fw-bold m-0 small">1. {{ $comment ?? '' }}</label>
                {!! Form::select($model, $list_pevaluacions, old($model), [
                    'wire:model' => $model,
                    'class' => 'form-select',
                    'id' => $model,
                    'placeholder' => 'Seleccione',
                ]) !!}
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>
        </div>
    </div>

    @if ($activity->pevaluacion_id)
        <div class="row py-2">
            <div class="col-12">
                <div class="form-group text-start ">
                    @php
                        $name = 'finicial';
                        $model = 'activity.' . $name;
                    @endphp
                    <label for="{{ $model }}" class="fw-bold m-0 small">2.
                        {{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::date($model, old($model), ['wire:model' => $model, 'class' => 'form-control', 'id' => $model]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        <div class="row -py-1">
            <div class="col-12">
                <div class="form-group text-start ">
                    @php
                        $name = 'ffinal';
                        $model = 'activity.' . $name;
                    @endphp
                    <label for="{{ $model }}" class="fw-bold m-0 small">3.
                        {{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::date($model, old($model), ['wire:model' => $model, 'class' => 'form-control', 'id' => $model]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        @if ($activity->finicial && $activity->ffinal)

            <div class="row -py-1">
                <div class="col-12">
                    <div class="form-group text-start ">
                        @php
                            $name = 'topic';
                            $model = 'activity.' . $name;
                        @endphp
                        <label for="{{ $model }}" class="fw-bold m-0 small">4.
                            {{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::textarea($model, old($model), [
                            'wire:model' => $model,
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

            <div class="row py-2">
                <div class="col-12">
                    <div class="form-group text-start ">
                        @php
                            $name = 'thematic';
                            $model = 'activity.' . $name;
                        @endphp
                        <label for="{{ $model }}" class="fw-bold m-0 small">5.
                            {{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::textarea($model, old($model), [
                            'wire:model' => $model,
                            'class' => 'form-control',
                            'placeholder' => $list_comment[$name],
                            'rows' => '3',
                        ]) !!}
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

            @if ($activity->topic && $activity->thematic)

                <div class="row py-2">
                    <div class="col-12">
                        <div class="form-group text-start ">
                            @php
                                $name = 'references';
                                $model = 'activity.' . $name;
                            @endphp
                            <label for="{{ $model }}" class="fw-bold m-0 small">6.
                                {{ $list_comment[$name] ?? '' }}</label>
                            {!! Form::textarea($model, old($model), [
                                'wire:model' => $model,
                                'class' => 'form-control',
                                'placeholder' => $list_comment[$name],
                                'rows' => '3',
                            ]) !!}
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="row py-2">

                    <div class="col-12">
                        <div class="form-group text-start ">
                            @php
                                $name = 'observations';
                                $model = 'activity.' . $name;
                            @endphp
                            <label for="{{ $model }}" class="fw-bold m-0 small">7.
                                {{ $list_comment[$name] ?? '' }}</label>
                            {!! Form::textarea($model, old($model), [
                                'wire:model' => $model,
                                'class' => 'form-control',
                                'placeholder' => $list_comment[$name],
                                'rows' => '3',
                            ]) !!}
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                @if ($activity->references && $activity->observations)
                    <div class="row py-2">
                        <div class="col-12">
                            <div class="form-group text-start ">
                                @php
                                    $name = 'description';
                                    $model = 'activity.' . $name;
                                @endphp
                                <label for="{{ $model }}" class="fw-bold m-0 small">8.
                                    {{ $list_comment[$name] ?? '' }}</label>
                                {!! Form::textarea($model, old($model), [
                                    'wire:model' => $model,
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

                    <div class="row py-2">
                        <div class="col-12">
                            <div class="form-group text-start ">
                                @php
                                    $name = 'teaching';
                                    $model = 'activity.' . $name;
                                @endphp
                                <label for="{{ $model }}" class="fw-bold m-0 small">9.
                                    {{ $list_comment[$name] ?? '' }}</label>
                                {!! Form::textarea($model, old($model), [
                                    'wire:model' => $model,
                                    'class' => 'form-control',
                                    'placeholder' => $list_comment[$name],
                                    'rows' => '6',
                                ]) !!}
                                @error($model)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="row py-2">
                        <div class="col-12">
                            <div class="form-group text-start">
                                @php
                                    $name = 'learning';
                                    $model = 'activity.' . $name;
                                @endphp
                                <label for="{{ $model }}" class="fw-bold m-0 small">40.
                                    {{ $list_comment[$name] ?? '' }}</label>
                                {!! Form::textarea($model, old($model), [
                                    'wire:model' => $model,
                                    'class' => 'form-control',
                                    'placeholder' => $list_comment[$name],
                                    'rows' => '6',
                                ]) !!}
                                @error($model)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
                @endif

            @endif

        @endif

    @endif



</div>



{{--
pevaluacion_id
finicial
ffinal
topic
thematic
references
observations

teaching
learning
description
    --}}
