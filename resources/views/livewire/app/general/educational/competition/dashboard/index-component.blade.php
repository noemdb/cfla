<div>    
    
    <section class="bg-center bg-no-repeat bg-[url('https://flowbite.s3.amazonaws.com/docs/jumbotron/conference.jpg')] bg-gray-700 bg-blend-multiply">

        <div class="px-4 mx-auto text-center h-full">
    
            @if ($competition)                
    
                <div class="flex items-center justify-center">
                    @include('livewire.app.general.educational.competition.partials.competition')                     
                </div>

                <x-select label="Select Status" placeholder="Seleccione Grado/Año" wire:model.live="grado_id" :options="$list_grado" option-value="id" option-label="name" />

                <livewire:app.general.educational.competition.dashboard.debate-component :competition_id="$competition->id"/>
                
            @else
    
                <div class="flex items-center justify-center mt-10">
                    @include('general.educational.competition.board.default.notfound')            
                </div>
    
            @endif        
    
        </div>
    
    </section>

</div>
