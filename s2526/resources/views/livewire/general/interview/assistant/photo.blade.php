{{-- <input type="file" wire:model="photo"> --}}

<div class="mt-3 border-top">
    <div class="mb-3">
        <label for="formFile" class="form-label"><span class="fw-bold">Foto tipo carnet del representante</span> [Opcional]</label>
        <input class="form-control" type="file" id="formFile" wire:model="photo">
    </div>
     
    @error('photo') <span class="error">{{ $message }}</span> @enderror

    @if ($photo)

        <div class="d-flex align-items-center flex-column mb-3">
            <div>Vista previa de la imagen:</div>  
            <div><img class="img-fluid rounded-top" src="{{ $photo->temporaryUrl() }}"></div>
        </div>       
        
    @else
    <div class="d-flex align-items-center flex-column mb-3">
        <div class="fw-bold text-muted">Foto de ejemplo:</div>  
        <div><img class="img-fluid rounded-top" src="{{ asset('images/avatar/estudiant/male.png') }}" style="width: 124"></div>
    </div> 
    @endif
    
</div>

