<fieldset class="border rounded p-2 my-2">
    <legend>V.- Aspectos Institucionales:</legend>
    <div class="form-group">
        @php $name = 'knowledge_of_school' ; $model = 'catchment_interview.'.$name @endphp
        <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
        <input type="text" class="form-control" id="knowledge_of_school" name="knowledge_of_school" wire:model.defer="{{$model}}">
        @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
    </div>
    <div class="row my-2">
        <div class="form-group col-md-6">
            <div class="form-check">
                @php $name = 'studied_at_school' ; $model = 'catchment_interview.'.$name @endphp
                <input class="form-check-input" type="checkbox" id="studied_at_school" name="studied_at_school" wire:model.defer="{{$model}}" >
                <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            </div>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="form-group col-md-6">
            @php $name = 'year_of_graduation' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="number" class="form-control" id="year_of_graduation" name="year_of_graduation" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>

    <div class="row my-4">
        <div class="form-group col-md-6">
            @php $name = 'academic_director' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="text" class="form-control" id="academic_director" name="academic_director" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="form-group col-md-6">
            @php $name = 'school_director' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="text" class="form-control" id="school_director" name="school_director" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>
    <div class="form-group">
        @php $name = 'teachers_worked_at_school' ; $model = 'catchment_interview.'.$name @endphp
        <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
        <textarea class="form-control" id="teachers_worked_at_school" name="teachers_worked_at_school" wire:model.defer="{{$model}}" rows="3"></textarea>
        @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
    </div>
    <div class="form-group">
        @php $name = 'reason_for_choosing_institution' ; $model = 'catchment_interview.'.$name @endphp
        <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
        <textarea class="form-control" id="reason_for_choosing_institution" name="reason_for_choosing_institution" wire:model.defer="{{$model}}"
            rows="3"></textarea>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
    </div>
    <div class="row my-2">
        <div class="form-group col-md-23">
            <div class="form-check">
                @php $name = 'recommendation_from_school' ; $model = 'catchment_interview.'.$name @endphp
                <input class="form-check-input" type="checkbox" id="recommendation_from_school" wire:model.defer="{{$model}}" name="recommendation_from_school" >
                <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            </div>
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>        
    </div>



    <div class="row my-4 border-bottom">

        <div class="form-group col-md-12">
            <div class="form-check">
                @php
                    $name = 'family_member_studied_worked_at_school';
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
        <div class="form-group col-md-6">
            @php $name = 'recommender_name' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="text" class="form-control" id="recommender_name" name="recommender_name" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="form-group col-md-6">
            @php $name = 'recommender_affinity' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="text" class="form-control" id="recommender_affinity" name="recommender_affinity" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
        <div class="form-group col-md-6">
            @php $name = 'recommender_phone' ; $model = 'catchment_interview.'.$name @endphp
            <label for="{{$model}}">{{$list_comment[$name] ?? null}}</label>
            <input type="text" class="form-control" id="recommender_phone" name="recommender_phone" wire:model.defer="{{$model}}">
            @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
        </div>
    </div>
</fieldset>