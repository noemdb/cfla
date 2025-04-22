<div>

    <div wire:poll.1s="updateOptions({{$competition->id}})">

        @if ($question)
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2"> 
                
                <div class="col-span-1">

                    <div class="flex items-center h-full mt-4">
                        <div class="rotate-[-90deg] text-2xl">Opciones</div>
                    </div>
                    
                </div>

                <div class="col-span-8"> 

                    @if ($question->status_answer)
                    <div class="my-2 border-t-4 border-green-800">
                    @else
                    <div class="my-2 border-t-4 border-yellow-300">
                    @endif                    
                    
                        <div class="grid border border-gray-200 rounded-lg shadow-sm p-2 dark:border-gray-700 md:grid-cols-2 bg-white dark:bg-gray-800">
                            @forelse ($options as $item)

                                @php
                                    $wrong = ($item->status_wrong_answer && $question->status_over_time) ? "background-color:red;" : null;
                                    $status_answer = ($item->status_option_correct && $question->status_over_time) ? true : false;
                                    $status_noanswer = ($question->status_over_time && ! $item->status_option_correct) ? true : false;
                                @endphp
                                
                                <figure style="{{ ($status_answer) ? "background-color:#3cb992;" : null}} {{$wrong ?? null}}"  class="text-gray-500  flex flex-col  items-center justify-start py-2 text-center bg-white border-b border-gray-200 rounded-t-lg md:rounded-t-none md:rounded-ss-lg md:border-e dark:bg-gray-800 dark:border-gray-700">                    
                                    <blockquote class="max-w-2xl mx-auto mb-2 lg:mb-2 dark:text-gray-400" >
                                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white pt-2">
                                            @if ($status_noanswer)
                                            <div class="rounded-full h-20 w-20 bg-gray-400 shadow-md hover:shadow-lg flex justify-center items-center">    
                                            @else
                                            <div class="rounded-full h-20 w-20 bg-indigo-950 shadow-md hover:shadow-lg flex justify-center items-center">    
                                            @endif                                            
                                                <span class="text-white text-center font-bold text-4xl">{{$literal[$loop->index]}}</span>
                                            </div>
                                        </h3>
                                    </blockquote>
                                    <figcaption class="flex items-center justify-center ">
                                        <div class="text-black space-y-0.5 font-medium dark:text-white text-left rtl:text-right ms-3">
                                            @if ($status_noanswer)
                                            <div class="text-gray-500 font-extralight text-2xl text-center">
                                            @else
                                            <div class="text-black font-extrabold text-4xl text-center">
                                            @endif                                            
                                                {!!$item->text!!}
                                            </div>
                                            <small class="text-gray-200 block">{{$item->id}}</small>
                                        </div>
                                    </figcaption>
                                </figure>
                            @empty
                                <div class="me-2 text-gray-400">Espere a que se establezca la pregunta activa</div>
                            @endforelse            
                        </div>

                    </div>

                </div>

                <div class="col-span-3" wire:poll.1s="updateTimetimeRemaining" style=""> 
                    
                    <div class="">
                        
                        @if ($competition)
                            @include('livewire.app.general.educational.competition.scoreboard.partials.countdown')
                            @include('livewire.app.general.educational.competition.scoreboard.partials.results')
                        @else
                            <div class="me-2 text-gray-400">Espere a que se establezca una competición</div>
                        @endif             

                    </div>

                </div> 

            </div>
        @else
            <div>Espere... No hay una pregunta activa</div>
        @endif

    </div>

</div>
