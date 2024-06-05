<div>    
    
    {{-- <section class="bg-center bg-no-repeat bg-[url('https://flowbite.s3.amazonaws.com/docs/jumbotron/conference.jpg')] bg-gray-700 bg-blend-multiply"> --}}
        
    <section class="">

        <div class="px-4 mx-auto text-center h-full">
    
            @if ($competition)   

                <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">
                    <div class="col-span-2"> 
                        {{-- @include('general.educational.competition.scoreboard.partials.competition') --}}
                        <livewire:app.general.educational.competition.scoreboard.competition-component :id="$competition->id"/>
                    </div>
                    <div class="col-span-2"> 
                        <div> <livewire:app.general.educational.competition.scoreboard.debate-component :id="$competition->id"/> </div>
                    </div>
                </div>

                {{-- <div> <livewire:app.general.educational.competition.scoreboard.question-component :id="$competition->id"/> </div> --}}

                {{-- <div> <livewire:app.general.educational.competition.scoreboard.option-component :id="$competition->id"/> </div> --}}
                
            @else
    
                <div class="flex items-center justify-center mt-10">

                    @include('general.educational.competition.scoreboard.default.notfound')            

                </div>
    
            @endif        
    
        </div>
    
    </section>

</div>


