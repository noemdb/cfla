<section>
    <h2>Información sobre el compromiso con la educación</h2>

    <div class="form-group pb-2">
        @php $name = 'importance_education'; $model = 'catchment.' . $name; @endphp
        <label for="{{$model}}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <textarea class="form-control" id="{{$model}}" name="{{$model}}" wire:model.defer="{{$model}}"></textarea>
        @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
    </div>

    <div class="form-group pb-2">
        @php $name = 'expectations_education'; $model = 'catchment.' . $name; @endphp
        <label for="{{$model}}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <textarea class="form-control" id="{{$model}}" name="{{$model}}" wire:model.defer="{{$model}}"></textarea>
        @error($model)<span class="text-danger small">{{ $message }}</span> @enderror  
    </div>  

    <div class="form-group pb-2">
        @php $name = 'participation_activities'; $model = 'catchment.' . $name; @endphp
        <label for="{{$model}}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <select class="form-select" id="{{$model}}" name="{{$model}}" wire:model.defer="{{$model}}">
            <option value="">Seleccionar</option>
            <option value="si">Sí</option>
            <option value="no">No</option>
        </select>
        @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
    </div>

</section>