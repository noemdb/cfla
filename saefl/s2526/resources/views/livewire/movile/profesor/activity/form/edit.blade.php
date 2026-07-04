<div class="alert alert-warning alert-dismissible fade show" role="alert">
    <strong>Editar la actividad</strong>
    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close" wire:click="closeActivity()"></button>
</div>

<div class="border rounded">
    @include('livewire.movile.profesor.activity.form.fields')            
        
    <div class="input-group mt-2">
    
        {!! Form::button('Guardar', ['class' => 'form-control btn btn-primary', 'wire:click' => 'saveActivity()']) !!}
    
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show small text-start" role="alert">
        <button type="button" class="btn-close small py-2" data-bs-dismiss="alert" aria-label="Close"></button>
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

