<fieldset class="border rounded p-2 my-2">
    <legend>I.- Datos del Representante:</legend>
    <div class="row my-2">
        <div class="form-group col-md-6">
            @php $name = 'full_name' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="text" class="form-control" id="full_name" name="full_name" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
        <div class="form-group col-md-6">
            @php $name = 'identification_number' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="text" class="form-control" id="identification_number" name="identification_number" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
    </div>
    <div class="row my-2">
        <div class="form-group col-md-6">
            @php $name = 'age' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="number" class="form-control" id="age" name="age" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
        <div class="form-group col-md-6">
            @php $name = 'relationship' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <select class="form-control" id="relationship" name="relationship" wire:model.defer="{{$model}}">
                <option value="">Seleccione una opción</option>
                <option value="Padre">Padre</option>
                <option value="Madre">Madre</option>
                <option value="Hermano">Hermano</option>
                <option value="Abuelo(a)">Abuelo(a)</option>
            </select>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
    </div>
    <div class="row my-2">
        <div class="form-group col-md-6">
            @php $name = 'phone_numbers' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="text" class="form-control" id="phone_numbers" name="phone_numbers" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
        <div class="form-group col-md-6">
            @php $name = 'email' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="email" class="form-control" id="email" name="email" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
        <div class="form-group col-md-6">
            @php $name = 'email_alternate' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="email" class="form-control" id="email_alternate" name="email_alternate" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
    </div>
    <div class="form-group">
        @php $name = 'profession_occupation' ; $model = 'catchment_interview.'.$name @endphp
        <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
        <input type="text" class="form-control" id="profession_occupation" name="profession_occupation" wire:model.defer="{{$model}}">
        @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
    </div>
</fieldset>