
<fieldset class="border rounded p-2 my-2">

    <legend>VI.- De ser aceptado en el colegio, se compromete a:</legend>

    <div class="alert alert-warning border-warning shadow-sm">
        <h6 class="font-weight-bold"><i class="fas fa-info-circle"></i> Aviso Financiero Importante</h6>
        <p class="mb-1">Estimado representante, para la reserva del cupo debe cancelar un monto de <strong>120 USD</strong>.</p>
        <p class="mb-0 small text-muted">Se estima un ajuste de matrícula entre 10% y 15% para el próximo periodo escolar.</p>
    </div>

    <div class="row my-2">
        <div class="form-group col-md-12">
            <div class="form-check">
                @php $name = 'agreement_to_code_of_conduct' ; $model = 'catchment_interview.'.$name @endphp
                <input class="form-check-input" type="checkbox" id="{{$model}}" name="{{$model}}"  wire:model.defer="{{$model}}">
                <label class="form-check-label" for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            </div>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row my-2">
        <div class="form-group col-md-12">
            <div class="form-check">
                @php $name = 'respect_communication_channels' ; $model = 'catchment_interview.'.$name @endphp
                <input class="form-check-input" type="checkbox" id="{{$model}}" name="{{$model}}"  wire:model.defer="{{$model}}">
                <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            </div>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row my-2">
        <div class="form-group col-md-12">
            <div class="form-check">
                @php $name = 'ensure_compliance_with_school_activities' ; $model = 'catchment_interview.'.$name @endphp
                <input class="form-check-input" type="checkbox" id="{{$model}}" name="{{$model}}"  wire:model.defer="{{$model}}">
                <label class="form-check-label" for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            </div>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row my-2">
        <div class="form-group col-md-12">
            <div class="form-check">
                @php $name = 'comply_with_school_uniform' ; $model = 'catchment_interview.'.$name @endphp
                <input class="form-check-input" type="checkbox" id="{{$model}}" name="{{$model}}"  wire:model.defer="{{$model}}">
                <label class="form-check-label" for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            </div>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row my-2">
        <div class="form-group col-md-12">
            <div class="form-check">
                @php $name = 'respect_authorities_directives' ; $model = 'catchment_interview.'.$name @endphp
                <input class="form-check-input" type="checkbox" id="{{$model}}" name="{{$model}}"  wire:model.defer="{{$model}}">
                <label class="form-check-label" for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            </div>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row my-2">
        <div class="form-group col-md-12">
            <div class="form-check">
                @php $name = 'pay_installments_on_time' ; $model = 'catchment_interview.'.$name @endphp
                <input class="form-check-input" type="checkbox" id="{{$model}}" name="{{$model}}"  wire:model.defer="{{$model}}">
                <label class="form-check-label" for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            </div>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row my-2">
        <div class="form-group col-md-12">
            <div class="form-check">
                @php $name = 'respect_parent_assembly_agreements' ; $model = 'catchment_interview.'.$name @endphp
                <input class="form-check-input" type="checkbox" id="{{$model}}" name="{{$model}}"  wire:model.defer="{{$model}}">
                <label class="form-check-label" for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            </div>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row my-2">
        <div class="form-group col-md-12">
            <div class="form-check">
                @php $name = 'pay_overdue_installments' ; $model = 'catchment_interview.'.$name @endphp
                <input class="form-check-input" type="checkbox" id="{{$model}}" name="{{$model}}"  wire:model.defer="{{$model}}">
                <label class="form-check-label" for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            </div>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>

</fieldset>