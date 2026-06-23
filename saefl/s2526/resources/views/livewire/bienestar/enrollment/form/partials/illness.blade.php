{{--
    
'status_illness_cardiovascular','status_illness_cancer','status_illness_lupus','status_illness_diabetes','status_illness_renal_problems','status_illness_overweight','status_illness_other','illness_other',
'status_conditions_intellectual_disability','status_conditions_motor_disability','status_conditions_visual_disability','status_conditions_hearing_impairment','status_conditions_outstanding_attitudes',
'status_conditions_autism','status_conditions_other','conditions_other','status_treated_by_specialist','specialist','status_take_medication','medication',

--}}

<ul class="list-group list-group-flush">

    <li class="list-group-item">
        <div class="text-dark"> Presenta alguna enfermedad de gravedad </div>

        <div class="row">

            <div class="col">

                <div class="row py-1">
                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    @php
                                        $name = 'status_illness_cardiovascular';
                                        $model = 'enrollment.' . $name;
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

                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    @php
                                        $name = 'status_illness_cancer';
                                        $model = 'enrollment.' . $name;
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

                <div class="row py-1">
                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    @php
                                        $name = 'status_illness_lupus';
                                        $model = 'enrollment.' . $name;
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

                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    @php
                                        $name = 'status_illness_diabetes';
                                        $model = 'enrollment.' . $name;
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

                <div class="row py-1">
                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    @php
                                        $name = 'status_illness_renal_problems';
                                        $model = 'enrollment.' . $name;
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

                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    @php
                                        $name = 'status_illness_overweight';
                                        $model = 'enrollment.' . $name;
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

                <div class="row py-1">
                    <div class="col">
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <div class="input-group-text">
                                    @php
                                        $name = 'status_illness_other';
                                        $model = 'enrollment.' . $name;
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

                <div class="row py-1">
                    <div class="col">
                        <div class="form-group">
                            @php
                                $name = 'illness_other';
                                $model = 'enrollment.' . $name;
                            @endphp
                            <label for="footer"
                                class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                            {!! Form::text($name, old($name), [
                                'wire:model.defer.defer' => $model,
                                'class' => 'form-control',
                                'placeholder' => $list_comment[$name],
                            ]) !!}
                            @error($model)
                                <span class="text-danger small">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

            </div>

        </div>
    </li>

    <li class="list-group-item">

        <div class="text-dark"> Presenta alguna de estas condiciones: </div>

        <div class="row py-1">
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_conditions_intellectual_disability';
                                $model = 'enrollment.' . $name;
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
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_conditions_motor_disability';
                                $model = 'enrollment.' . $name;
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

        <div class="row py-1">
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_conditions_visual_disability';
                                $model = 'enrollment.' . $name;
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
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_conditions_hearing_impairment';
                                $model = 'enrollment.' . $name;
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

        <div class="row py-1">
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_conditions_outstanding_attitudes';
                                $model = 'enrollment.' . $name;
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
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_conditions_autism';
                                $model = 'enrollment.' . $name;
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

        <div class="row py-1">
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_conditions_other';
                                $model = 'enrollment.' . $name;
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

        <div class="row py-1">
            <div class="col">
                <div class="form-group">
                    @php
                        $name = 'conditions_other';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::text($name, old($name), [
                        'wire:model.defer.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

    </li>

    <li class="list-group-item">

        <div class="row py-1">
            <div class="col">
                <div class="input-group">
                    <div class="input-group-prepend">
                        <div class="input-group-text">
                            @php
                                $name = 'status_treated_by_specialist';
                                $model = 'enrollment.' . $name;
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

        <div class="row py-1">
            <div class="col">
                <div class="form-group">
                    @php
                        $name = 'specialist';
                        $model = 'enrollment.' . $name;
                    @endphp
                    <label for="footer"
                        class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                    {!! Form::text($name, old($name), [
                        'wire:model.defer.defer' => $model,
                        'class' => 'form-control',
                        'placeholder' => $list_comment[$name],
                    ]) !!}
                    @error($model)
                        <span class="text-danger small">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>
    </li>


</ul>
