<div>

    <div class="m-2 p-2 border rounded">
    
        @if (session()->has('message'))
            <div class="alert alert-success p-4 m-4">
                {{ session('message') }}
            </div>
        @endif
        <div class="float-right ">
            <small wire:loading.delay.shortest class="text-muted small px-2">
                Procesando...
            </small> 
        </div>
        
        @includeWhen($modeIndex,'livewire.administracion.blog.index')
        @includeWhen($modeCreate,'livewire.administracion.blog.create')
        @includeWhen($modeEdit,'livewire.administracion.blog.edit')

    </div>

</div>
{{--
modeIndex
modeShow
modeEdit
modeCreate
--}}