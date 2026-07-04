<div>

    <div class="text-center">
        <h4>La Cédula, pieza fundamental de la vida</h4>        
    </div>    
    
    <div class="container-fluid">

        <div class="row">

            <div class="col-md-8 offset-md-2 col-lg-6 offset-lg-3 col-xl-6 offset-xl-3 px-1">

                @switch($mode)
                    @case('initial') @include('livewire.general.instrument.partials.initial') @break
                    @case('resume') @include('livewire.general.instrument.partials.resume') @break
                    @case('basicTerms') @include('livewire.general.instrument.partials.basicTerms') @break
                    @case('structures') @include('livewire.general.instrument.partials.structures') @break
                    @case('comprende') @include('livewire.general.instrument.partials.comprende') @break
                    @case('applies') @include('livewire.general.instrument.partials.applies') @break
                    @case('analyzes') @include('livewire.general.instrument.partials.analyzes') @break
                    @case('evaluates') @include('livewire.general.instrument.partials.evaluates') @break
                    @case('create') @include('livewire.general.instrument.partials.create') @break
                    @case('final') @include('livewire.general.instrument.partials.final') @break
                @endswitch               

            </div>
            
        </div>

    </div>

    <div wire:loading> 
        <div class="position-relative">
            <div class="position-absolute bottom-0 end-0">
                <div class="clearfix">
                    <div class="spinner-border float-end" role="status">
                    <span class="visually-hidden">Procesando...</span>
                </div>
            </div>    
        </div>
    </div>

</div>
