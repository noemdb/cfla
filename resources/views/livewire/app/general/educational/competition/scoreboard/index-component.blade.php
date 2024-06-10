<div>    
    
    {{-- <section class="bg-center bg-no-repeat bg-[url('https://flowbite.s3.amazonaws.com/docs/jumbotron/conference.jpg')] bg-gray-700 bg-blend-multiply"> --}}
        
    <section class="">

        <div class="px-4 mx-auto text-center p-2">
    
            @if ($competition)   

                <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">

                    <div class="col-span-10 mr-1"> 

                        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">

                            <div class="col-span-2"> 
                                <livewire:app.general.educational.competition.scoreboard.competition-component :id="$competition->id"/>
                            </div>
        
                            <div class="col-span-2"> 
                                <div>
                                    <livewire:app.general.educational.competition.scoreboard.debate-component :id="$competition->id"/>
                                </div>
                            </div>
        
                        </div>

                        <div class="border-t-4 border-cyan-600 mt-2 bg-slate-100">
        
                            <div>
                                <livewire:app.general.educational.competition.scoreboard.question-component :id="$competition->id"/>
                            </div>
            
                            <div>
                                <livewire:app.general.educational.competition.scoreboard.option-component :id="$competition->id"/>
                            </div>

                        </div>

                    </div>

                    <div class="col-span-2 ml-1 border-l-2">
                        <div class="bg-lime-200 border-t-4 border-green-600 p-2" wire:poll.5s="updateScoreBoard({{$competition->id}})">
                            <span class="text-xl font-bold">Tabla General de Resultados:</span>                            
                        </div>
                        @include('livewire.app.general.educational.competition.scoreboard.partials.scores')
                    </div>

                </div>                
                
            @else
    
                <div class="flex items-center justify-center mt-10">

                    @include('livewire.app.general.educational.competition.scoreboard.default.notfound') 

                </div>
    
            @endif        
    
        </div>
    
    </section>

</div>


