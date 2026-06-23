<div class=" p-2 border rounded mb-2">

    <h5>Datos de enfermedads</h5>

    <ul class="list-group list-group-flush">

        <li class="list-group-item">
            <div class="text-dark"> Presenta alguna enfermedad de gravedad </div>

            <div class="row">

                <div class="col-sm-12">

                    <div class="row py-1">
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        @php
                                            $name = 'status_illness_cardiovascular';
                                            $model = '' . $name;
                                        @endphp
                                        <input type="checkbox" name="{{ $name }}" value="1">
                                    </div>
                                </div>
                                <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                                @error($model)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        @php
                                            $name = 'status_illness_cancer';
                                            $model = '' . $name;
                                        @endphp
                                        <input type="checkbox" name="{{ $name }}" name="{{ $name }}"
                                            value="1">
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
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        @php
                                            $name = 'status_illness_lupus';
                                            $model = '' . $name;
                                        @endphp
                                        <input type="checkbox" name="{{ $name }}" value="1">
                                    </div>
                                </div>
                                <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                                @error($model)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        @php
                                            $name = 'status_illness_diabetes';
                                            $model = '' . $name;
                                        @endphp
                                        <input type="checkbox" name="{{ $name }}" value="1">
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
                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        @php
                                            $name = 'status_illness_renal_problems';
                                            $model = '' . $name;
                                        @endphp
                                        <input type="checkbox" name="{{ $name }}" value="1">
                                    </div>
                                </div>
                                <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                                @error($model)
                                    <span class="text-danger small">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        @php
                                            $name = 'status_illness_overweight';
                                            $model = '' . $name;
                                        @endphp
                                        <input type="checkbox" name="{{ $name }}" value="1">
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
                                            $model = '' . $name;
                                        @endphp
                                        <input type="checkbox" name="{{ $name }}" value="1">
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
                                    $model = '' . $name;
                                @endphp
                                <label for="footer"
                                    class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                                {!! Form::text($name, old($name), ['class' => 'form-control', 'placeholder' => $list_comment[$name]]) !!}
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
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                @php
                                    $name = 'status_conditions_intellectual_disability';
                                    $model = '' . $name;
                                @endphp
                                <input type="checkbox" name="{{ $name }}" value="1">
                            </div>
                        </div>
                        <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                @php
                                    $name = 'status_conditions_motor_disability';
                                    $model = '' . $name;
                                @endphp
                                <input type="checkbox" name="{{ $name }}" value="1">
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
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                @php
                                    $name = 'status_conditions_visual_disability';
                                    $model = '' . $name;
                                @endphp
                                <input type="checkbox" name="{{ $name }}" value="1">
                            </div>
                        </div>
                        <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                @php
                                    $name = 'status_conditions_hearing_impairment';
                                    $model = '' . $name;
                                @endphp
                                <input type="checkbox" name="{{ $name }}" value="1">
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
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                @php
                                    $name = 'status_conditions_outstanding_attitudes';
                                    $model = '' . $name;
                                @endphp
                                <input type="checkbox" name="{{ $name }}" value="1">
                            </div>
                        </div>
                        <div class="form-control"> {{ $list_comment[$name] ?? null }}</div>
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                @php
                                    $name = 'status_conditions_autism';
                                    $model = '' . $name;
                                @endphp
                                <input type="checkbox" name="{{ $name }}" value="1">
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
                                    $model = '' . $name;
                                @endphp
                                <input type="checkbox" name="{{ $name }}" value="1">
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
                            $model = '' . $name;
                        @endphp
                        <label for="footer"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::text($name, old($name), ['class' => 'form-control', 'placeholder' => $list_comment[$name]]) !!}
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

        </li>

        <li class="list-group-item">

            <div class="row py-1">
                <div class="col-sm-12">
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <div class="input-group-text">
                                @php
                                    $name = 'status_treated_by_specialist';
                                    $model = '' . $name;
                                @endphp
                                <input type="checkbox" name="{{ $name }}" value="1">
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
                <div class="col-sm-12">
                    <div class="form-group">
                        @php
                            $name = 'specialist';
                            $model = '' . $name;
                        @endphp
                        <label for="footer"
                            class=" font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
                        {!! Form::text($name, old($name), ['class' => 'form-control', 'placeholder' => $list_comment[$name]]) !!}
                        @error($model)
                            <span class="text-danger small">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>

        </li>


    </ul>

</div>
