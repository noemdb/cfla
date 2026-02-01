<div>

    {{-- <section class="bg-center bg-no-repeat bg-[url('https://flowbite.s3.amazonaws.com/docs/jumbotron/conference.jpg')] bg-gray-700 bg-blend-multiply"> --}}
    <section class="">

        <div class="px-4 mx-auto text-center h-full">

            @if ($competition)

                <div class="flex items-center justify-center">
                    @include('livewire.app.general.educational.competition.moderator.partials.competition')
                </div>

                {{-- <x-select placeholder="Seleccione Grado/Año" wire:model.live="grado_id" :options="$list_grado" option-value="id" option-label="name" /> --}}

                <!-- Academic Structure Section -->
                <div class="mt-8 mb-4">
                    <h3
                        class="text-sm font-bold text-emerald-400 uppercase tracking-[0.2em] mb-6 flex items-center justify-center">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path d="M12 14l9-5-9-5-9 5 9 5z" />
                            <path
                                d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z" />
                        </svg>
                        Estructura Académica
                    </h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @forelse ($peducativos as $peducativo)
                            <div
                                class="diagnostic-card bg-gray-900/40 backdrop-blur-md border border-emerald-500/10 rounded-2xl p-5 hover:border-emerald-500/40 transition-all duration-300">
                                <div class="flex items-center space-x-3 mb-4 border-b border-emerald-500/10 pb-3">
                                    <div class="bg-emerald-500/10 p-2 rounded-lg">
                                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                        </svg>
                                    </div>
                                    <h4 class="text-sm font-bold text-white uppercase tracking-tight">
                                        {{ $peducativo->name }}</h4>
                                </div>

                                <div class="flex flex-wrap gap-2">
                                    @php $grados = $peducativo->grados @endphp
                                    @forelse ($grados as $item)
                                        <button wire:click="setGrado({{ $item->id }})"
                                            class="px-3 py-1.5 rounded-lg text-xs font-bold transition-all duration-200 border
                                                {{ $item->id == $grado_id
                                                    ? 'bg-emerald-500 text-white border-emerald-400 shadow-[0_0_15px_rgba(16,185,129,0.4)]'
                                                    : 'bg-gray-800/60 text-gray-400 border-gray-700 hover:border-emerald-500/50 hover:text-emerald-300' }}">
                                            {{ $item->name }}
                                        </button>
                                    @empty
                                        <span class="text-xs text-gray-500 italic">No hay grados disponibles</span>
                                    @endforelse
                                </div>
                            </div>
                        @empty
                            <div
                                class="col-span-full py-8 bg-gray-900/40 rounded-2xl border border-dashed border-gray-700">
                                <p class="text-gray-500">No hay Planes de Estudios activos</p>
                            </div>
                        @endforelse
                    </div>
                </div>

                @includeWhen(
                    $grado_id,
                    'livewire.app.general.educational.competition.moderator.partials.results')

                <livewire:app.general.educational.competition.moderator.debate-component :competition_id="$competition->id" />
            @else
                <div class="flex items-center justify-center mt-10">

                    @include('general.educational.competition.moderator.default.notfound')

                </div>

            @endif

        </div>

    </section>

</div>
