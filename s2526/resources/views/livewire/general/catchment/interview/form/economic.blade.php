<fieldset class="border rounded p-2 my-2">
    <legend>IV.- Aspectos Socio-Económicos:</legend>
    <div class="row my-2">
        <div class="form-group col-md-6">
            @php
                $name = 'monthly_income';
                $model = 'catchment_interview.' . $name;
            @endphp
            <label for="{{ $model }}">{{ $list_comment[$name] ?? null }}</label>
            {!! Form::select($name, $list_monthly_income, old($model), [
                'wire:model.defer' => $model,
                'id' => $model,
                'name' => $model,
                'class' => 'form-select',
                'placeholder' => 'Seleccione',
            ]) !!}
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
        <div class="form-group col-md-6">
            @php
                $name = 'num_people_contributing';
                $model = 'catchment_interview.' . $name;
            @endphp
            <label for="{{ $model }}">{{ $list_comment[$name] ?? null }}</label>
            <input type="number" class="form-control" id="num_people_contributing" name="num_people_contributing"
                wire:model.defer="{{ $model }}">
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>
    <div class="form-group">
        @php
            $name = 'income_source';
            $model = 'catchment_interview.' . $name;
        @endphp
        <label for="{{ $model }}">{{ $list_comment[$name] ?? null }}</label>
        <input type="text" class="form-control" id="income_source" name="income_source"
            wire:model.defer="{{ $model }}">
        @error($model)
            <span class="text-danger small">{{ $message }}</span>
        @enderror
    </div>
    <div class="row my-2">
        <div class="form-group col-md-12">
            <div class="form-check">
                @php
                    $name = 'able_to_pay_dollars';
                    $model = 'catchment_interview.' . $name;
                @endphp
                <input class="form-check-input" type="checkbox" id="{{ $model }}" name="{{ $model }}"
                    wire:model.defer="{{ $model }}">
                <label class="form-check-label" for="{{ $model }}">{{ $list_comment[$name] }}</label>
            </div>
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group col-md-12">
            <div class="form-check">
                @php
                    $name = 'able_to_pay_bolivars';
                    $model = 'catchment_interview.' . $name;
                @endphp
                <input class="form-check-input" type="checkbox" id="{{ $model }}" name="{{ $model }}"
                    wire:model.defer="{{ $model }}">
                <label class="form-check-label" for="{{ $model }}">{{ $list_comment[$name] }}</label>
            </div>
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="p-2 border rounded">

        <div class="row my-2">

            <div class="form-group col-md-12">
                <div class="form-check">
                    @php
                        $name = 'has_payment_responsible';
                        $model = 'catchment_interview.' . $name;
                    @endphp
                    <input class="form-check-input" type="checkbox" id="{{ $model }}"
                        name="{{ $model }}" wire:model.defer="{{ $model }}">
                    <label class="form-check-label" for="{{ $model }}">{{ $list_comment[$name] }}</label>
                </div>
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

        </div>

        <div class="row my-2">

            <div class="form-group col-md-12">
                @php
                    $name = 'person_guarantor_name_phone';
                    $model = 'catchment_interview.' . $name;
                @endphp
                <label for="{{ $model }}">{{ $list_comment[$name] }}</label>
                <input type="text" class="form-control" id="{{ $model }}" name="{{ $model }}"
                    wire:model.defer="{{ $model }}">
                @error($model)
                    <span class="text-danger small">{{ $message }}</span>
                @enderror
            </div>

        </div>

    </div>

</fieldset>
