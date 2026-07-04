<section>
    {{-- <h2>Información sobre el perfil del estudiante</h2>

    <div class="form-group pb-2">
        @php $name = 'skills_talents'; $model = 'catchment.' . $name;  @endphp
        <label for="{{$model}}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <textarea class="form-control" id="{{$model}}" name="{{$model}}" wire:model.defer="{{$model}}"></textarea>
        @error($model)<span class="text-danger small mb-2">{{ $message }}</span> @enderror
    </div>

    <div class="form-group pb-2">
        @php $name = 'interests'; $model = 'catchment.' . $name;  @endphp
        <label for="{{$model}}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <textarea class="form-control" id="{{$model}}" name="{{$model}}" wire:model.defer="{{$model}}"></textarea>
        @error($model)<span class="text-danger small mb-2">{{ $message }}</span> @enderror
    </div>

    <div class="form-group pb-2">
        @php $name = 'challenges'; $model = 'catchment.' . $name;  @endphp
        <label for="{{$model}}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <textarea class="form-control" id="{{$model}}" name="{{$model}}" wire:model.defer="{{$model}}"></textarea>
        @error($model)<span class="text-danger small mb-2">{{ $message }}</span> @enderror
    </div> --}}

    <div class="form-group pb-2">
        <div class="input-group">
            <div class="input-group-prepend">
                <div class="input-group-text">
                    @php $name = 'status_accept_terms' ; $model= 'catchment.'.$name @endphp
                    <input type="checkbox" wire:model="{{$model ?? null}}">
                </div>
            </div>
            <div class="form-control">
                Me comprometo a participar en cada fase del proceso. Asimismo, estoy consciente de que el censo no representa una garantía del cupo en la institución.
            </div>
        </div>
    </div>
    @error($model)<span class="text-danger small">{{ $message }}</span> @enderror

    <h2>Toda la información esta lista para ser registrada.</h2>  
    
    <div class="alert alert-secondary p-2 form-control fw-bold">Haz click en el botón enviar para finalizar el registro.</div>

</section>

<button type="button" wire:click="save" class="btn btn-success w-100 my-4">Enviar</button>
