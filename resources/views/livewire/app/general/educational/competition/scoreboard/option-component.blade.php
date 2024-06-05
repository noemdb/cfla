<div>

    <div >

        <div class="grid grid-cols-1 sm:grid-cols-4 gap-2">        

            <div class="col-span-3" wire:poll.1s="updateOptions({{$competition->id}})" style=""> 

                <div id="alert-border-3" class="text-start flex items-center justify-center px-2 my-2 text-yellow-800 border-t-4 border-yellow-300 bg-yellow-50 dark:text-yellow-400 dark:bg-gray-800 dark:border-yellow-800" role="alert">
                    <div class="ms-3 text-sm font-bold">
                        Opciones:
                    </div>
                </div>
                
                <div class="grid mb-2 border border-gray-200 rounded-lg shadow-sm dark:border-gray-700 md:mb-4 md:grid-cols-2 bg-white dark:bg-gray-800">
                    @forelse ($options as $item)
                        <figure {{ ($item->status_option_correct && $question->status_over_time) ? "style=background-color:lime" : null}} class="flex flex-col items-center justify-start py-2 text-center bg-white border-b border-gray-200 rounded-t-lg md:rounded-t-none md:rounded-ss-lg md:border-e dark:bg-gray-800 dark:border-gray-700">                    
                            <blockquote class="max-w-2xl mx-auto mb-2 text-gray-500 lg:mb-2 dark:text-gray-400">
                                <h3 class="text-lg font-semibold text-gray-900 dark:text-white pt-2">
                                    @php $color=$colors[0]; @endphp
                                    <div class="text-8xl rounded-full rou border p-2 bg-cyan-600">{{$literal[$loop->index]}}</div>
                                    {{-- <x-badge.circle :$color lg label="{{$literal[$loop->index]}}"/> --}}
                                </h3>
                            </blockquote>
                            <figcaption class="flex items-center justify-center ">
                                <div class="space-y-0.5 font-medium dark:text-white text-left rtl:text-right ms-3">
                                    <div class="font-bold text-5xl text-gray-500 dark:text-gray-400 text-center">
                                        {{$item->text}}
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

            <div class="col-span-1" wire:poll.1s="updateTimetimeRemaining" style=""> 
                
                <div class="">

                    <div id="alert-border-3" class="text-start flex items-center justify-center px-2 my-2 text-blue-800 border-t-4 border-blue-300 bg-blue-50 dark:text-blue-400 dark:bg-gray-800 dark:border-blue-800" role="alert">
                        <div class="ms-3 text-sm font-bold">
                            Reloj, cuenta regresiva:
                        </div>
                    </div>

                    
                    @include('livewire.app.general.educational.competition.scoreboard.component.answer')
                    
                    {{-- @include('livewire.app.general.educational.competition.scoreboard.partials.results') --}}
                    
                    {{-- <livewire:app.general.educational.competition.scoreboard.answer-component :id="$competition->id"/> --}}

                   

                </div>

            </div> 

        </div>

    </div>

</div>
