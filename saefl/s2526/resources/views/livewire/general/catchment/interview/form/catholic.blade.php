<fieldset class="border rounded p-2 my-2">
    <legend>VII.- Aspectos Religiosos:</legend>

    <div class="row my-2">
        <div class="form-group col-md-12">
            @php
                $name = 'religion';
                $model = 'catchment_interview.' . $name;
            @endphp
            <label for="{{ $model }}">{{ $list_comment[$name] }}</label>
            {!! Form::select($name, $list_religions, old($model), [
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
    </div>

    <div class="row my-2">
        <div class="form-group col-md-6">
            <div class="form-check">
                @php
                    $name = 'agreement_to_catholic_formation';
                    $model = 'catchment_interview.' . $name;
                @endphp
                <input class="form-check-input" type="checkbox" id="agreement_to_catholic_formation"
                    name="agreement_to_catholic_formation" wire:model.defer="{{ $model }}">
                <label class="form-check-label" for="{{ $model }}">{{ $list_comment[$name] ?? null }}</label>
            </div>
            @error($model)
                <span class="text-danger small">{{ $message }}</span>
            @enderror
        </div>
    </div>

    <div class="row my-2">
        <div class="form-group col-md-12">
            <div class="form-check">
                @php
                    $name = 'awareness_of_catholic_school_affiliation';
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

    <div class="row my-2">
        <div class="form-group col-md-12">
            <div class="form-check">
                @php
                    $name = 'agreement_to_participate_in_catholic_activities';
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

    <div class="row my-2">
        <div class="form-group col-md-12">
            @php
                $name = 'justification_for_not_participating_in_catholic_activities';
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

</fieldset>

{{-- religion
awareness_of_catholic_school_affiliation
agreement_to_participate_in_catholic_activities
justification_for_not_participating_in_catholic_activities --}}
