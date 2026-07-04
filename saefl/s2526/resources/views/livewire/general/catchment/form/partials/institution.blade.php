<section>            

    <h2>Información sobre el interés en nuestro Complejo Educativo CFLA</h2>

    {{-- <div class="form-group pb-2">
        @php $name = 'reason_catholic';  $model = 'catchment.' . $name; @endphp
        <label for="{{$model}}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <textarea class="form-control" id="{{$model}}" name="{{$model}}" wire:model.defer="{{$model}}"></textarea>
        @error($model)<span class="text-danger small">{{ $message }}</span> @enderror 
    </div>     --}}

    <div class="form-group pb-2">
        @php $name = 'reason_interest'; $model = 'catchment.' . $name; @endphp
        <label for="{{$model}}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <textarea class="form-control" id="{{$model}}" name="{{$model}}" wire:model.defer="{{$model}}"></textarea>
        @error($model)<span class="text-danger small">{{ $message }}</span> @enderror  
    </div>    

    {{-- <div class="form-group pb-2">
        @php $name = 'aspects_valued'; $model = 'catchment.' . $name;  @endphp
        <label for="{{$model}}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <textarea class="form-control" id="{{$model}}" name="{{$model}}" wire:model.defer="{{$model}}"></textarea>
        @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
    </div> --}}
    
    {{-- <div class="form-group pb-2">
        @php $name = 'expectations'; $model = 'catchment.' . $name;  @endphp
        <label for="{{$model}}" class="font-weight-bold m-0 small">{{ $list_comment[$name] ?? '' }}</label>
        <textarea class="form-control" id="{{$model}}" name="{{$model}}" wire:model.defer="{{$model}}"></textarea>
        @error($model)<span class="text-danger small">{{ $message }}</span> @enderror
    </div> --}}

</section>