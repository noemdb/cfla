<fieldset class="border rounded p-2 my-2">
    <legend>III.- Información sobre quién vive con el representado:</legend>
    <div class="row my-2">
        <div class="form-group col-md-6">
            @php $name = 'living_with' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <select class="form-select" id="living_with" name="living_with" wire:model.defer="{{$model}}">
                <option value="">Seleccione una opción</option>
                <option value="Madre">Madre</option>
                <option value="Padre">Padre</option>
                <option value="Ambos">Ambos</option>
                <option value="Hermano(a)">Hermano(a)</option>
                <option value="Otro">Otro</option>
            </select>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
        <div class="form-group col-md-6">
            @php $name = 'other_person_origin' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="text" class="form-control" id="other_person_origin" name="other_person_origin" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
    </div>
    <div class="row my-2">
        <div class="form-group col-md-12">
            @php $name = 'reason_for_living_with_other' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <textarea class="form-control" id="reason_for_living_with_other" name="reason_for_living_with_other" wire:model.defer="{{$model}}" rows="3"></textarea>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
    </div>
    <div class="row my-2">
        <div class="form-group col-md-6">
            @php $name = 'num_family_group_members' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="number" class="form-control" id="num_family_group_members" name="num_family_group_members" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
        <div class="form-group col-md-6">
            @php $name = 'num_people_financially_dependent' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="number" class="form-control" id="num_people_financially_dependent" name="num_people_financially_dependent" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
        </div>
    </div>
    <div class="p-2 border rounded">
        <div class="row my-2">
            <div class="form-group col-xl-6">
                @php $name = 'person_responsible_attending' ; $model = 'catchment_interview.'.$name @endphp
                <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
                <input type="text" class="form-control" id="person_responsible_attending" name="person_responsible_attending" wire:model.defer="{{$model}}">
                @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
            </div>
            <div class="form-group col-xl-6">
                @php $name = 'place_person_responsible_attending' ; $model = 'catchment_interview.'.$name @endphp
                <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
                <input type="text" class="form-control" id="place_person_responsible_attending" name="place_person_responsible_attending" wire:model.defer="{{$model}}">
                @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
            </div>
        </div>
        <div class="row my-2">
            <div class="form-group col-md-12">
                @php $name = 'position_person_responsible_attending' ; $model = 'catchment_interview.'.$name @endphp
                <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
                <input type="text" class="form-control" id="position_person_responsible_attending" name="position_person_responsible_attending" wire:model.defer="{{$model}}">
                @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
            </div>
        </div>
    </div>
</fieldset>