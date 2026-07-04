<div>
    <div class="row mb-3">
        <div class="col-md-6">
            <button type="button" class="btn btn-primary" wire:click="create()">Crear</button>
        </div>
        <div class="col-md-6">
            <div class="d-flex">
                <input type="text" class="form-control" placeholder="Buscar..." wire:model="search" />
                <span class="input-text px-4"  wire:loading>
                    <strong>Procesando...</strong>                              
                </span>
            </div>
        </div>
    </div>

    @if (session()->has('message')) <div class="alert alert-success"> {{ session('message') }} </div> @endif

    <div class="container-fluid">

        <div class="row">

            <div class="col-sm-{{$modeIndex ? '12':'4'}}">

                @include('livewire.administracion.cobranza.coll-calendar.table.index')

            </div> 

            @if ($modeCreate)

                <div class="col-sm-8">

                    @include('livewire.administracion.cobranza.coll-calendar.form.create')

                </div>

            @endif

            @if ($modeEdit)

                <div class="col-sm-8">
                    @include('livewire.administracion.cobranza.coll-calendar.form.edit')

                </div>

            @endif            
             
        </div>

    </div>    
    
</div>


