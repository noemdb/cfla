<div>

    <div wire:poll.1s="updateOptions({{ $competition->id }})">

        @if ($question)
            <div class="grid grid-cols-1 sm:grid-cols-12 gap-2">

                <div class="col-span-1">

                    <div class="flex items-center h-full mt-4">
                        <div class="rotate-[-90deg] text-2xl">Opciones</div>
                    </div>

                </div>

                <div class="col-span-8">

                    @if ($question->status_answer)
                        <div class="my-2 border-t-2 border-emerald-500">
                        @else
                            <div class="my-2 border-t-2 border-orange-400">
                    @endif

                    <div
                        class="grid border border-emerald-500/20 rounded-2xl shadow-lg p-4 md:grid-cols-2 bg-gray-900/40 backdrop-blur-sm h-full gap-4">
                        @forelse ($options as $item)
                            @php
                                $wrong = $item->status_wrong_answer && $question->status_over_time;
                                $status_answer = $item->status_option_correct && $question->status_over_time;
                                $status_noanswer = $question->status_over_time && !$item->status_option_correct;
                            @endphp

                            <div
                                class="diagnostic-card h-full flex flex-col items-center justify-start py-6 text-center rounded-2xl border transition-all duration-500 relative overflow-hidden
                                    {{ $status_answer
                                        ? 'bg-gradient-to-br from-emerald-600/50 to-emerald-500/30 border-emerald-300 shadow-2xl shadow-emerald-500/60 scale-105 animate-pulse'
                                        : ($wrong
                                            ? 'bg-red-500/20 border-red-500/30 opacity-60'
                                            : 'bg-gray-800/40 border-emerald-500/10') }}">

                                @if ($status_answer)
                                    <!-- Icono de check para respuesta correcta -->
                                    <div class="absolute top-2 right-2 bg-white rounded-full p-1.5 shadow-lg">
                                        <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    </div>
                                @endif
                                <div class="max-w-2xl mx-auto mb-4 h-full flex flex-col justify-center z-10">
                                    <div class="mb-4">
                                        @if ($status_noanswer)
                                            <div
                                                class="rounded-full h-20 w-20 bg-gray-600/50 shadow-md flex justify-center items-center mx-auto">
                                            @else
                                                <div
                                                    class="rounded-full {{ $status_answer ? 'h-28 w-28 bg-gradient-to-br from-white to-emerald-100 shadow-2xl shadow-emerald-400/50 ring-4 ring-emerald-300/50' : 'h-20 w-20 bg-emerald-600 shadow-lg' }} flex justify-center items-center mx-auto transition-all duration-500">
                                        @endif
                                        <span
                                            class="{{ $status_answer ? 'text-emerald-700 text-6xl' : 'text-white text-4xl' }} text-center font-black">{{ $literal[$loop->index] }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center justify-center h-full px-4 z-10">
                                <div class="space-y-1">
                                    @if ($status_noanswer)
                                        <div
                                            class="text-gray-500 font-light text-xl text-center line-through opacity-50">
                                        @else
                                            <div
                                                class="{{ $status_answer ? 'text-white font-black text-3xl drop-shadow-lg' : 'text-white font-bold text-2xl' }} text-center transition-all duration-500">
                                    @endif
                                    {!! $item->text !!}
                                </div>
                                <small class="text-gray-600 block text-xs">#{{ $item->id }}</small>
                            </div>
                    </div>
                </div>
            @empty
                <div class="col-span-2 text-gray-500 italic py-8">Espere a que se establezca la pregunta activa</div>
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
